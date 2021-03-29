# Outage Alert
# Application Server
# AppModelDB
#  
# Connects to the database for CRUD functions
# 
# Notable References:
# https://pynative.com/python-mysql-transaction-management-using-commit-rollback/
#
# ---------------------------------------------------------------------------------------

import mysql.connector
import json                            # Provides encoding and decoding functions for JSON strings/files
from mysql.connector import Error
from mysql.connector import errorcode

import AppTimeLib                      # Custom date and time related functions for the OutageAlert application


# Variable definitions
mydb = ''
mycursor = ''
verbose = False #used for verbose output to the terminal for testing purposes. Change to 'True' to see output.
defaultDB = 'TestDB'


def OpenDBConnection(dbname = defaultDB):
    """Opens a new connection to the MySQL Database"""

    global mydb
    global mycursor

    mydb = mysql.connector.connect(
    host = "localhost",
    user = "OutageAlert",
    password = "VqD4fDBJtt40iwFP",
    database = dbname
    )
    
    mydb.autocommit = False # Prevent SQL executions from automatically committing. Allows for a rollback if something goes wrong.
    mycursor = mydb.cursor(dictionary=True) # Rows from SELECT statements will be returned as Python dictionaries
    if verbose: print("DB connection is open.")
    #return (mydb, mycursor) # Might not need this...

#(mydb, mycursor) = OpenDBConnection() #saved in case we need it


def CloseDBConnection():
    """Closes an existing database connection"""

    global mydb
    global mycursor

    if(mydb.is_connected()):
        mycursor.close()
        mydb.close()
        if verbose: print("DB connection is closed.")




def GetOutageList(values):
    """
    Retrieves all outages from the database that have an ID listed in the supplied parameter.
    
    Input: a list of BC Hydro outage ID numbers.
    Output: a tuple of two components: 
        The first component is a list of dictionaries, each dict represents a database record for an outages that had a matching ID number
        The second component is an error message, if applicible.
    """
    global mydb
    global mycursor

    myresult = []
    err = None

    if len(values) == 0:
        return (myresult, err)
    

    try:
        OpenDBConnection()
        
        sql = "SELECT * FROM Outage WHERE OutageID IN " + str(tuple(values)) + " ORDER BY OutageID ASC;"

        # "str(tuple(values))" is a tuple of id numbers, created from the supplied list. 
        # This is the format that MySQL needs in order to process process the SELECT statement in one request. 

        # Execute the SQL command
        mycursor.execute(sql) 

        # Retrieve all of the results
        myresult = mycursor.fetchall() 

        # Parse all json strings into dictionaries for easy handling in Python
        for i in range(len(myresult)):
            myresult[i]['json'] = json.loads(myresult[i]['Json'])


    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to retrieve outage list: {error}"

    finally:
        CloseDBConnection()

    return (myresult, err)



def GetActiveOutageIDList():
    """
    Retrieves all outage ID numbers from the database that are currently active.
    
    Input: none
    Output: a tuple of two components: 
        The first component is a list of outage numbers
        The second component is an error message, if applicible.
    """
    global mydb
    global mycursor

    myresult = []
    err = None  

    try:
        OpenDBConnection()
        
        sql = "SELECT OutageID FROM Outage WHERE `Outage Status` = 1 ORDER BY OutageID ASC;"

        # Execute the SQL command
        mycursor.execute(sql) 

        # Retrieve all of the results
        myresult = mycursor.fetchall() 

        # Parse all json strings into dictionaries for easy handling in Python
        for i in range(len(myresult)):
            myresult[i] = myresult[i]['OutageID']


    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to retrieve outage list: {error}"

    finally:
        CloseDBConnection()

    return (myresult, err)



def SaveNewOutages(outageList, jsonTime):
    """
    Takes a list of new outage dictionaries and saves them to the database.

    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are saved to the database. All changes are rolled back.
    """
    global mydb
    global mycursor

    err = None
    jsonTimeDB = AppTimeLib.DateTimeFromPythonToMySQL(jsonTime)

    try:
        OpenDBConnection()

        # SQL INSERT command
        sql = "INSERT INTO Outage (OutageID, Json, `Outage Time`, `Json Time`, `Outage Status`) VALUES (%s, %s, %s, %s, %s);"

        # For each new outage, insert a record for it into the database using the supplied values with the above SQL command
        for outage in outageList:
            outageTime = AppTimeLib.DateTimeFromJSToMySQL(outage['dateOff'])
            values = (outage['id'], json.dumps(outage), outageTime, jsonTimeDB, 1)
            mycursor.execute(sql, values)
        
        # If INSERT commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to insert record to database rollback: {error}"
        # Database rollback because of exception - reverses all executed INSERT commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err



def UpdateOutage(outageUpdateList, jsonTime):
    """
    Takes a list of outage dictionaries that have been updated and saves the updates to the database

    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are updated in the database.
    """
    global mydb
    global mycursor
    
    err = None
    jsonTimeDB = AppTimeLib.DateTimeFromPythonToMySQL(jsonTime)
    currentTime = AppTimeLib.GetCurrentUTCTime()


    try:
        OpenDBConnection()

        # SQL UPDATE command
        sql = "UPDATE Outage SET Json = %s, `Json Time` = %s, `Outage Status` = %s WHERE OutageID = %s;"

        # For each updated outage, update the record for it in the database using the supplied values with the above SQL command
        for outage in outageUpdateList:

            # Set the Outage Status to 1='True', unless the outage shows that power has been restored then set to 0='False' and dateOn is not future-dated.
            outageStatus = 1
            if outage['dateOn'] != None:
                if AppTimeLib.DateTimeFromJSToPython(outage['dateOn']) <= currentTime:
                    outageStatus = 0
            
            values = (json.dumps(outage), jsonTimeDB, outageStatus, outage['id'])
            mycursor.execute(sql, values)
        
        # If UPDATE commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to insert record to database rollback: {error}"
        # Database rollback because of exception - reverses all executed UPDATE commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err



def CancelOutage(outageIDCancelSet):
    """
    Takes a set of outages that are no longer being reported and cancells them in the database

    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are updated in the database.
    """
    global mydb
    global mycursor
    
    err = None

    try:
        OpenDBConnection()

        # SQL UPDATE command
        sql = "UPDATE Outage SET `Outage Status` = 0 WHERE OutageID = %s;"

        # For each updated outage, update the record for it in the database using the supplied values with the above SQL command
        for id in outageIDCancelSet:

            values = (id,)
            mycursor.execute(sql, values)
        
        # If UPDATE commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to cancel outages in database - rollback: {error}"
        # Database rollback because of exception - reverses all executed UPDATE commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err




def GetProperties():
    """
    Retrieves all active properties from the database.
    
    Input: None.
    Output: all database property columns & records
    """
    global mydb
    global mycursor

    myresult = []
    err = None
    

    try:
        OpenDBConnection()
        
        sql = "SELECT * FROM Property"
        
        # Execute the SQL command
        mycursor.execute(sql) 

        # Retrieve all of the results
        myresult = mycursor.fetchall() 

    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to retrieve outage list: {error}"

    finally:
        CloseDBConnection()

    return (myresult, err)



def InsertPropertyOutages(propOutageList):
    """
    Inserts a record into the `Property Outage` associative table in the DB which represents a link between 
    an outage and a property. 
    
    Input: A list of dictionaries containing the property ID's and outage ID's. [{'outageID': number, 'propertyID': number}, ... ]
    Output: None or error object
    """
    global mydb
    global mycursor

    err = None
    isOutageActive = 1
    
    try:
        OpenDBConnection()

        # SQL INSERT command
        sql = "INSERT INTO `Property Outage` (OutageID, PropertyID, Active) VALUES (%s, %s, %s);"

        # For each new outage, insert a record for it into the database using the supplied values with the above SQL command
        for outage in propOutageList:
            values = (outage['outageID'], outage['propertyID'], isOutageActive)
            mycursor.execute(sql, values)
        
        # If INSERT commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to insert record to database rollback: {error}"
        # Database rollback because of exception - reverses all executed INSERT commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err


def UpdatePropertyOutages(outageList, isOutageActive):
    """
    Updates a record in the `Property Outage` associative table in the DB which represents a link between 
    an outage and a property. 
    
    Input:  A list of dictionaries containing the property ID's and outage ID's: [{'outageID': number, 'propertyID': number}, ... ]
            and a boolean 1='True' or 0='False' to indicate if the outage is active.
    Output: None or error object
    """
    global mydb
    global mycursor

    err = None
    
    try:
        OpenDBConnection()

        # SQL INSERT command
        sql = "UPDATE `Property Outage` SET Active = %s WHERE OutageID = %s;"

        # For each new outage, insert a record for it into the database using the supplied values with the above SQL command
        for outage in outageList:
            values = (isOutageActive, outage['id'])
            mycursor.execute(sql, values)
        
        # If INSERT commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to update property outage record to {isOutageActive}: {error}"
        # Database rollback because of exception - reverses all executed INSERT commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err




# Retrieve user contact settings & property info from DB for updated/new outages (add DB functionality to AppModelDB.py)                <----
def GetOutageUsersByEmail():
    """
    Retrieve user email contact settings & property info from DB for updated/new outages
    
    Input: None.
    Output: A dictionary containing PropertyID, `Property Name`, Address, Recipients.Name, Recipients.`Contact Email`
    """
    global mydb
    global mycursor

    myresult = []
    err = None
    

    try:
        OpenDBConnection()
        
        sql =  "SELECT Property.PropertyID, Property.`Property Name`, Property.Address, Recipients.Name, Recipients.`Contact Email`, `Property Outage`.OutageID "
        sql += "FROM (((`Property Outage` INNER JOIN Property ON `Property Outage`.PropertyID = Property.PropertyID) "
        sql += "INNER JOIN `Recipient Properties` ON Property.PropertyID = `Recipient Properties`.PropertyID) "
        sql += "INNER JOIN Recipients ON Recipients.Name = `Recipient Properties`.Name AND Recipients.AccountID = `Recipient Properties`.AccountID) "
        sql += "WHERE `Property Outage`.Active = True AND Recipients.`Contact Email` IS NOT NULL AND (`Recipient Properties`.Active = 2 OR `Recipient Properties`.Active = 3) "
        sql += "ORDER BY Recipients.`Contact Email` ASC, Recipients.Name ASC;"
        
        # Execute the SQL command
        mycursor.execute(sql) 

        # Retrieve all of the results
        myresult = mycursor.fetchall() 

    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to retrieve user contact settings & property info from DB for updated/new outages: {error}"

    finally:
        CloseDBConnection()

    return (myresult, err)




# Retrieve user contact settings & property info from DB for updated/new outages (add DB functionality to AppModelDB.py)                <----
def GetOutageUsersByPhone():
    """
    Retrieve user phone contact settings & property info from DB for updated/new outages
    
    Input: None.
    Output: A dictionary containing PropertyID, `Property Name`, Address, Recipients.Name, Recipients.Phone
    """
    global mydb
    global mycursor

    myresult = []
    err = None
    

    try:
        OpenDBConnection()
        
        sql =  "SELECT `Property Outage`.OutageID, Property.PropertyID, Property.`Property Name`, Property.Address, Recipients.Name, Recipients.Phone, Recipients.Provider "
        sql += "FROM (((`Property Outage` INNER JOIN Property ON `Property Outage`.PropertyID = Property.PropertyID) "
        sql += "INNER JOIN `Recipient Properties` ON Property.PropertyID = `Recipient Properties`.PropertyID) "
        sql += "INNER JOIN Recipients ON Recipients.Name = `Recipient Properties`.Name AND Recipients.AccountID = `Recipient Properties`.AccountID) "
        sql += "WHERE `Property Outage`.Active = True "
        sql +=      "AND Recipients.Phone IS NOT NULL "
        sql +=      "AND Recipients.Provider IS NOT NULL "
        sql +=      "AND (`Recipient Properties`.Active = 1 OR `Recipient Properties`.Active = 3) "
        sql += "ORDER BY Recipients.Phone ASC, Recipients.Name ASC;"
        
        # Execute the SQL command
        mycursor.execute(sql) 

        # Retrieve all of the results
        myresult = mycursor.fetchall() 

    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = f"Failed to retrieve user contact settings & property info from DB for updated/new outages: {error}"

    finally:
        CloseDBConnection()

    return (myresult, err)