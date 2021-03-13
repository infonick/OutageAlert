# Outage Alert
# Application Server
# AppModelGetOutages
#
# This script retrieves a JSON file from BC Hydro. The JSON file contains all current power outage data.
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
        


# URL Resources
url = 'https://www.bchydro.com/power-outages/app/outages-map-data.json'
#url = 'https://cs.tru.ca/~npilusof20/index.html' # Used for testing a failed connection




def RetrieveBCHydro ():
    # Retrieve the json file from BC Hydro --------------------------------------------------
    try:
        r = requests.get(url, allow_redirects=True)

    except requests.exceptions.ConnectionError:
        for line in sys.exc_info():
            print(line)
        # TODO: Program a routine that notifies the admin and/or records a server connection error                                    <----
        # TODO: Also need to handle what happens next with the program!

    except: 
        # For another type of error not explicitly caught above
        for line in sys.exc_info():
            print(line)
        # TODO: Program a routine that notifies the admin and/or records the error                                                    <----
        # TODO: Also need to handle what happens next with the program!



    # Test for retrieval success 
    try:
        r.raise_for_status() # Will raise an HTTPError if the HTTP request returned an 'unsuccessful' status code.    

    except:
        for line in sys.exc_info():
            print(line)
        # TODO: Program a routine that notifies the admin in the event a HTTP request error code ocours, etc.                         <----


    if len(r.history) != 0:
        # TODO: Program a routine that prompts an admin to review the byhydro json url. A redirect or other issue may be developing   <----
        # https://requests.readthedocs.io/en/master/user/quickstart/#redirection-and-history
        pass


    if r.headers.get('content-type') != 'application/json':
        # TODO: Program a routine to respond to an unkonwn file type that was retrieved                                               <----
        pass



    # Handle the retrieved JSON file ----------------------------------------------------------------------
    # Record time of JSON file retrieval
    try:
        # Get the time of the JSON file retrieval as a Python DateTime object
        currentTime = AppTimeLib.GetTimeFromURLHeader(r)

    except:
        currentTime = AppTimeLib.GetCurrentUTCTime() # Get the current time in UTC from the system if getting the time from the HTTP header is unsuccessful
        # TODO: Program a routine that notifies the admin that retrieving the time from the HTTP header did not work                  <----


    # Convert JSON file to a list of Python dictionaries:
    try:
        outages = r.json()

    except: 
        # Error in parsing the json file
        for line in sys.exc_info():
            print(line)
        # TODO: Program a routine that notifies the admin, etc.                                                                       <----
        # TODO: Another thought, can an injection to OutageAlert happen if the BCHydro json is compromised?                           <----
        # TODO: If the BCHydro json is empty (no outages, or no new outages) can we skip all other processing?

    return (outages, currentTime)




def SortOutages(outages):
    # Sort outages into new and existing categories -----------------------------------------

    # Make a list of outage id's from the JSON file
    outageIDList = []
    for outage in outages:
        outageIDList.append(outage['id'])

    # Retrieve existing outages from the database
    (dbOutages, err) = AppModelDB.GetOutageList(outageIDList)

    if err != None:
        # TODO: There was a problem retrieving the existing outages from the database. Handle this error                               <----
        print("ERROR RETRIEVING EXISTING OUTAGES FROM DATABASE!")

    else:
        # Make a list of outage id's that already exist in the database
        outageListRetrieved = []
        for outage in dbOutages:
            outageListRetrieved.append(outage['id'])

        # Sort JSON outages into two lists - new and existing outages
        newOutages = []
        existingOutages = []

        for outage in outages:
            if outage['id'] in outageListRetrieved: # If the outage already exists in the database
                existingOutages.append(outage.copy()) # Add it to the list of existing outages
            else:
                newOutages.append(outage.copy()) # Otherwise add it to a list of newly discovered outages

    return (newOutages, existingOutages, dbOutages)
