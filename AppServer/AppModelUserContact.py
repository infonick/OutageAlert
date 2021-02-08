# Outage Alert
# Application Server
# AppModelUserContact
#
# This script handles generating messages for users and sending messages to users.
# 
# ---------------------------------------------------------------------------------------

# Import custom modules
import AppTimeLib   # Custom date and time related functions for the OutageAlert application
import AppModelDB   # Database commands

# Import standard modules
import requests     # Allows Python to send HTTP requests, used to retrieve BC Hydro JSON file
import sys          # Used with requests module error handling
import datetime     # Provides datetime object functions
import json         # Provides encoding and decoding functions for JSON strings/files




# NEW POWER OUTAGES ---------------------------------------------------------------------
# Save new outage data to the database (Implemented in AppModelRetrieveJSON)

# err = AppModelDB.SaveNewOutages(newOutages)
# if err != None:
#     #There was a problem saving the new outages to the database. Handle this error                                        <----
#     print("NEW OUTAGE DATA NOT SAVED TO DATABASE!")
#     print(err)


# Alert users of new outages                                                                                                <----
# NEED TO COMPLETE THIS SECTION!
# NEED newOutages (list of outage dictionaries)




# EXISTING POWER OUTAGES ----------------------------------------------------------------
# NEED updateOutages (list of outage dictionaries)                                                                          <----
# Figure out what changed with each outage so we can act on those changes
for i in range(len(updateOutages)):
    (_, dbOutageInfo, updatedOutageInfo) = updateOutages[i] # Get the two outage dictionaries
    
    for key in updatedOutageInfo:                           # For each key in the dictionary...
        if updatedOutageInfo[key] == dbOutageInfo[key]:     # If the key values are the same...
            updateOutages[i].pop(key)                       # Remove the key & value from these dictionaries
            dbOutageInfo[i].pop(key)
            break
    if len(updatedOutageInfo) == len(dbOutageInfo) == 0:    # If an outage record has no keys left, remove it from the update list
        updateOutages.pop(i)


    
#Alert users to outage updates

updateAlerts = [] # This is a ist of tuples consisting of the outage ID number, and all the related update messages for that outage ID

for i in range(len(updateOutages)):
    (outageID, dbOutageInfo, updatedOutageInfo) = updateOutages[i]

    outageMessages = [] # This is a list of strings for the current outage ID number. Each string is a message to the user about a change in this specific power outage.

    for key in updatedOutageInfo:
        oldValue = dbOutageInfo[key]
        newValue = updatedOutageInfo[key]

        # Generate generic power outage update messages for users

        if key == 'cause':
            if oldValue == None:
                outageMessages.append("Cause of power outage: " + newValue)
            else:
                outageMessages.append("Cause of power outage updated from \'" + oldValue + "\' to \'" + newValue + "\'.")
            break


        elif key == 'crewStatusDescription':
            if oldValue == None:
                outageMessages.append("Power restoration crew status: " + newValue)
            else:
                outageMessages.append("Power restoration crew status updated from \'" + oldValue + "\' to \'" + newValue + "\'.")
            break


        elif key == 'crewEta':
            dateTimeCrewETA = AppTimeLib.DateTimeFromJSToPython(newValue)
            dateTimeCrewETA = AppTimeLib.PythonChangeTimeZone(dateTimeCrewETA, 'America/Vancouver')
            if oldValue == None:
                outageMessages.append("Power restoration crew ETA: " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            else:
                outageMessages.append("Power restoration crew ETA updated to: " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            break


        elif key == 'dateOff':
            dateTimeOff = AppTimeLib.DateTimeFromJSToPython(newValue)
            dateTimeOff = AppTimeLib.PythonChangeTimeZone(dateTimeOff, 'America/Vancouver')
            if oldValue == None:
                outageMessages.append("Power outage began on " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            else:
                outageMessages.append("Power outage start time was updated to " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            break


        elif key == 'dateOn':
            dateTimeOn = AppTimeLib.DateTimeFromJSToPython(newValue)
            dateTimeOn = AppTimeLib.PythonChangeTimeZone(dateTimeOn, 'America/Vancouver')
            if oldValue == None:
                outageMessages.append("Power restored on " + datetime.datetime.strftime(dateTimeOn, '%Y-%b-%d %I:%M:%S %p %Z'))
            else:
                outageMessages.append("Power restoration time was updated to " + datetime.datetime.strftime(dateTimeOn, '%Y-%b-%d %I:%M:%S %p %Z'))
            break


        # OTHER DICTIONARY 'KEY' OPTIONS, saved in case we want to use them.
        # elif key == 'polygon':
        #     # need to check for newly affected customers, or newly restored customers if the polygon changes                 <----
        #     break
        # elif key == 'lastUpdated':
        #     # What do we do with this part? anything?                                                                        <----
        #     break
        # elif key == 'numCustomersOut':
        #     break
        # elif key == 'id':
        #     break
        # elif key == 'gisId':
        #     break
        # elif key == 'regionId':
        #     break
        # elif key == 'municipality':
        #     break
        # elif key == 'area':
        #     break
        # elif key == 'regionName':
        #     break
        # elif key == 'crewEtr':
        #     break
        # elif key == 'showEta':
        #     break
        # elif key == 'showEtr':
        #     break
        # elif key == 'latitude':
        #     break
        # elif key == 'longitude':
        #     break
    
    updateAlerts.append((outageID, outageMessages)) # Create a tuple consisting of the outage ID number, and all the related update messages for that outage ID


#send the updateAlerts tuple-list to a function that sends the messages to our users                                        <----



