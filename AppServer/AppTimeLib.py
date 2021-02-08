# OutageAlert
# Application Server
# AppTimeLib
#
# This file contains a library of custom time functions for OutageAlert
# 
# ---------------------------------------------------------------------------------------

import datetime, pytz


# Module Variables ----------------------------------------------------------------------
# JavaScript stores dates as number of milliseconds since January 01, 1970, 00:00:00 UTC (Universal Time Coordinated).
JS_BASE_DATE_STR = '1970-01-01 00:00:00.000000 +0000'

# Create a python datetime object
JS_BASE_DATE_OBJ = datetime.datetime.strptime(JS_BASE_DATE_STR, '%Y-%m-%d %H:%M:%S.%f %z')




def DateTimeFromJSToPython (mills):
    """Converts from JavaScript Time in milliseconds to a Python DateTime object"""

    global JS_BASE_DATE_OBJ

    # Add the number of milliseconds that was submitted as a parameter and return the python datetime object
    AdjDate = JS_BASE_DATE_OBJ + datetime.timedelta(milliseconds=mills) 
    return AdjDate



def DateTimeFromPythonToJS (dtObject):
    """Converts from a Python DateTime object to JavaScript time in milliseconds"""

    global JS_BASE_DATE_OBJ

    # Get the difference between the Python DateTime Object and the JS base date
    timeDifference = dtObject - JS_BASE_DATE_OBJ

    # Calculate the number of milliseconds since the JS base date
    milliseconds =  timeDifference.days*86400000        # milliseconds in a day
    milliseconds += timeDifference.seconds*1000         # milliseconds in a second
    milliseconds += timeDifference.microseconds//1000   # milliseconds in a microsecond, using integer division

    return milliseconds


def PythonChangeTimeZone (UTCTime, offset):
    """Changes the time zone of a Python DateTime object to a particular offset"""

    # If the offset is a number, adds or subtracts number of minutes from UTC. For example, PST = pytz.FixedOffset(-8*60) for -8 hours
    if (type(offset) == type(-8)) or (type(offset) == type(-8.5)):
        newZone = pytz.FixedOffset(offset*60)
    
    # If the offset is a string representation of a time zone (eg: 'America/Vancouver'), then use the timezone function
    else: #if type(offset) == type('string'):
        newZone = pytz.timezone(offset)

    # Create new time in the desired timezone
    newDateTime = UTCTime.astimezone(newZone)
    return newDateTime



def GetTimeFromURLHeader (HTTP_request_resource):
    """
    Extracts the time from a HTTP header generated by the 'requests' module when making a URL request.

    Returns a Python DateTime object.
    """

    # Extract the header date
    headerDate = HTTP_request_resource.headers.get('Date')

    # Check that time zone is as expected
    if headerDate[-3:] == "GMT":
        # add UTC Time Zone so that datetime can recognise it
        headerDate = headerDate[:-3] + '+0000'

    else:
        raise Exception("HTTP_request_resource.headers.get('Date') did not have the expected time zone ('GMT')!")

    # Parse the headerDate string, explicitly identify its date format, and create a DateTime object
    dateTimeObject = datetime.datetime.strptime(headerDate, '%a, %d %b %Y %H:%M:%S %z')
    
    return dateTimeObject



def GetCurrentUTCTime():
    """Returns a Python DateTime object for the current time in UTC."""
    return datetime.datetime.now(pytz.utc)



# Some data and code used for testing
"""
jsmills = 1612723800000 #Sun Feb 07 2021 10:50:00 GMT-0800 (Pacific Standard Time)

newDate = DateTimeFromJSToPython(jsmills)
oldDate = DateTimeFromPythonToJS(newDate)

print("JS OLD:", jsmills)
print("JS NEW:", oldDate)
print("PY NEW:", newDate)
print("PY PST:", PythonChangeTimeZone(newDate, -8))
"""

# Correct Output for the above
"""
JS OLD: 1612723800000
JS NEW: 1612723800000
PY NEW: 2021-02-07 18:50:00+00:00
PY PST: 2021-02-07 10:50:00-08:00
"""


