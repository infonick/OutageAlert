# Outage Alert
# Application Server
# AppController
#
# This script is the main controller file
#   
# ---------------------------------------------------------------------------------------

# Import custom modules
import AppTimeLib   # Custom date and time related functions for the OutageAlert application
import AppModelDB   # Database commands
import AppModelOutageMessages # Functions related to generating and sending messages to users.
import AppModelGetOutages     # Functions related to retrieving and sorting outage information
import AppModelPolygonPointFunctions  # Functions for polygons and coordinates
import AppModelSESMail  # Functions for sending emails through SES


# Import standard modules
import requests     # Allows Python to send HTTP requests, used to retrieve BC Hydro JSON file
import sys          # Used with requests module error handling
import datetime     # Provides datetime object functions
import json         # Provides encoding and decoding functions for JSON strings/files
        

verbose = True


# Get a fresh list of outages and sort them into new/existing lists ---------------------

(outages, currentTime) = AppModelGetOutages.RetrieveBCHydro()

(newOutages, existingOutages, dbOutages, cancelledOutageIDSet) = AppModelGetOutages.SortOutages(outages)



# CANCELLED POWER OUTAGES ---------------------------------------------------------------
ListOfOutageMessages = AppModelOutageMessages.GenerateCancelledOutageMessages(cancelledOutageIDSet)
if verbose: print("ListOfOutageMessages (cancelled):", ListOfOutageMessages)



# NEW POWER OUTAGES ---------------------------------------------------------------------
# Save new outage data to the database 

err = AppModelDB.SaveNewOutages(newOutages, currentTime)

if err != None:
    # TODO:There was a problem saving the new outages to the database. Handle this error                                         <----
    print("NEW OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)

# Generate the messages we will send to users for the new outages
ListOfOutageMessages += AppModelOutageMessages.GenerateNewOutageMessages(newOutages)
if verbose: print("ListOfOutageMessages (new):", ListOfOutageMessages)


# TODO: Find a more efficient way to retrieve a more concise list of relevant properties from the DB
(allProperties, err) = AppModelDB.GetProperties()

if err != None:
    # TODO:There was a problem retrieving property info from the database. Handle this error                                     <----
    print("PROPERTY INFO NOT RETRIEVED FROM DATABASE!")
    print(err)


# Calculate which properties are inside which new outages and add a property-outage record to the DB

propOutageList = []

for property in allProperties:
    for outage in newOutages:
        if AppModelPolygonPointFunctions.PointInPolygon(property['Latitude'], property['Longitude'], outage['polygon']):
            propOutageList.append({'outageID': outage['id'], 'propertyID': property['PropertyID']})
            break
    # TODO: In the future, it may be a good idea to also check for properties that were added after an outage had started.

err = AppModelDB.InsertPropertyOutages(propOutageList)
if err != None:
    # TODO:There was a problem saving the new property-outages to the database. Handle this error                                <----
    print("NEW PROPERTY-OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)


# EXISTING POWER OUTAGES ----------------------------------------------------------------
# Compare old outages to find changes                                  

updateOutages = [] # Stores tuples of outages (Outage ID #, Outage Info in DB, Updated Outage Info)

dbOutagesCopy = dbOutages.copy() # For the binary search, work with a copy of the dbOutages list

for existingOutage in existingOutages:

    # Use a binary search to compare outage information from BC Hydro with outage information from the DB
    target = existingOutage['id']   # Target outage ID number
    left = 0                        # Left binary search index number
    right = len(dbOutagesCopy)-1    # Right binary search index number

    while left <= right:                                    # While the left and right numbers still provide a search option
        attempt = (left + right) // 2                       # Next index to attempt
        if target == dbOutagesCopy[attempt]['OutageID']:          # If the ID number matches
            dbOutageInfo = dbOutagesCopy[attempt]['json']    
            updatedOutageInfo = existingOutage
            for key in updatedOutageInfo:                           # Search for values that are not the same between the two instances of the outage
                if updatedOutageInfo[key] != dbOutageInfo[key]:
                    updateOutages.append((target, dbOutageInfo.copy(),updatedOutageInfo.copy())) # If a difference is found, add it to the update list
                    dbOutagesCopy.pop(attempt)                      # Remove this outage from the dbOutage list so that we don't have to search over it again.
                    break
            break
            
        elif target < dbOutagesCopy[attempt]['OutageID']:         # If the target ID number is less than the attempt index ID number
            right = attempt - 1                             # Move the right index marker for the binary search

        else:                                               # If the target ID number is greater than the attempt index ID number
            left = attempt + 1                              # Move the left index marker for the binary search
            
    
    if left > right:
        # TODO: Program a routine to deal with cases where two outage records did not coincide                                    <----
        print('ERROR: AN EXISTING OUTAGE WAS NOT FOUND IN THE DB RECORDS!')



# Update database with new outage information
dbUpdates = [outage for (_,_,outage) in updateOutages] #python list comprehension

err = AppModelDB.UpdateOutage(dbUpdates, currentTime)

if err != None:
    # TODO:There was a problem saving the updated outages to the database. Handle this error                                      <----
    print("UPDATED OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)


# Generate the messages we will send to users for the outage updates and add that to the list of outage messages.
ListOfOutageMessages += AppModelOutageMessages.GenerateOutageUpdateMessages(updateOutages)
if verbose:  print("ListOfOutageMessages (upd):",ListOfOutageMessages)



# SEND MESSAGES ----------------------------------------------------------------
(OutageUsersByEmail, err) = AppModelDB.GetOutageUsersByEmail()
if err != None:
    # TODO: There was a problem retrieving info from the database. Handle this error                                             <----
    print("ERROR RETREIVING OutageUsersByEmail!")

(OutageUsersByPhone, err) = AppModelDB.GetOutageUsersByPhone()
if err != None:
    # TODO: There was a problem retrieving info from the database. Handle this error                                             <----
    print("ERROR RETREIVING OutageUsersByPhone!")

if verbose:  print(f"OutageUsersByEmai: {OutageUsersByEmail}")
messages = AppModelSESMail.createEmailMessages(ListOfOutageMessages, OutageUsersByEmail)

if verbose:  print("Messages:",messages)
AppModelSESMail.sendOutageEmailsToUsers(messages)
print("SENT!")



# DEACTIVATE OUTAGES IN THE DB ---------------------------------------------------------------
# For any outages that have ended, disable the property-outage record in the DB
err = AppModelGetOutages.DeactivateOutages(existingOutages, cancelledOutageIDSet)
if err != None:
            # TODO: There was a problem updating the database. Handle this error (duplicate error handling also in AppModelGetOutages.DeactivateOutages())     <----
            print("AppModelGetOutages.DeactivateOutages(): ERROR Deactivating Outages IN DATABASE!")






# COMPLETE:
# Retrieve the json file from BC Hydro
# Handle the retrieved JSON file
# Sort outages into new and existing categories
# NEW POWER OUTAGES - save json to DB & generate user messages for each outage
# EXISTING POWER OUTAGES - save json to DB & generate user messages for each outage
# Calculate which properties are inside which new outages and add a PropertyOutage record to the DB (use AppModelPolygonPointFunctions.py && use InsertPropertyOutages in AppModelDB.py)
# Retrieve user contact settings & property info from DB for updated/new outages (add DB functionality to AppModelDB.py)
# Rename AppModelRetrieveJSON.py to AppController.py and move non-controller functions (json functions,etc) to their own Model files.
# Deactivate property-outages in the DB where power has been restored (use UpdatePropertyOutages in AppModelDB.py)
# Send outage messages in ListOfOutageMessages to the users (use functions in AppModelSESMail.py to send emails; use GetOutageUsersByPhone() & GetOutageUsersByEmail() )
# Find a more reliable way to deactivate outages from the database. it appears restored outages can just dissappear!
# Unit, Component, and System testing


# TODO: Reactivate property-outages in the DB where power has been lost again (use UpdatePropertyOutages in AppModelDB.py) (LOW PRIORITY)
# TODO: Search for properties that have been added since a power outage started and give them messages??? Has this been done?
# TODO: Implement sending sms messages


# OLD CODE ------------------------------------------------------------------------------

# Outage dictionary keys w/ examples
#         id: 1588375
#         gisId: 1578468
#         regionId: 1602964060
#         municipality: Burns Lake
#         area: M 32/3  WADDLE
#         cause: Wire down
#         numCustomersOut: 1
#         crewStatusDescription: Crew on-site
#         crewEta: 1612723800000
#         dateOff: 1612720680000
#         dateOn: 1612742400000 (or None if power is not restored)
#         lastUpdated: 1612729685000
#         regionName: Northern
#         crewEtr: 1612742400000
#         showEta: False
#         showEtr: True
#         latitude: 54.208241
#         longitude: -125.713181
#         polygon: [-125.713123, 54.207342, -125.712825, 54.207366, -125.712541, 54.207423, -125.712281, 54.207512, ...]





"""
print("Time: ", currentTime)

for outage in outages:
    if outage['dateOn'] !=  None:
        print('')
        for key in outage.keys():
            print('\t' + key + ":", outage[key]) 
    
    elif outage['dateOn'] ==  None:
        print('')
        for key in outage.keys():
            print('\t' + key + ":", outage[key]) 
"""


#print (outages.keys())

#print("Start: ", outages)

#ctString = currentTime.strftime('%Y-%m-%d %H:%M:%S.%f %z %Z')
#print("TIme:  " + ctString)
#myTime = dt.strptime(ctString, '%Y-%m-%d %H:%M:%S.%f %z %Z')
#print("Time2:", myTime)



#from pprint import pprint
#pprint(vars(r))

#print(r.json())
#print(r.headers.get('Date'))
#print(currentTime)
#print(r.headers.get('content-type')) #'application/json'






