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

import mysql.connector, json
from mysql.connector import Error
from mysql.connector import errorcode


# Variable definitions
outageTableName = "" #The name of the table that stores records of each power outage                                        <----
mydb = ''
mycursor = ''
verbose = False #used for verbose output to the terminal for testing purposes. Change to 'True' to see output.



def OpenDBConnection():
    """Opens a new connection to the MySQL Database"""

    global mydb
    global mycursor

    mydb = mysql.connector.connect(
    host="localhost",
    user="yourusername",#                                                                                                   <---- update db login info & name
    password="yourpassword",#                                                                                               <---- update db login info & name
    database="mydatabase"#                                                                                                  <---- update db login info & name
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
    

    try:
        OpenDBConnection()
        
        sql = 'SELECT * FROM ' + outageTableName + " WHERE 'id' IN " + str(tuple(values)) + " ORDER BY 'id' ASC;"
        # "str(tuple(values))" is a tuple of id numbers, created fromthe supplied list. 
        # This is the format that MySQL needs in order to process process the SELECT statement in one request. 

        # Execute the SQL command
        mycursor.execute(sql) 

        # Retrieve all of the results
        myresult = mycursor.fetchall() 

        # Parse all json strings into dictionaries for easy handling in Python
        for i in range(len(myresult)):
            myresult[i]['json'] = json.loads(myresult[i]['json'])


    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = "Failed to retrieve outage list: {}".format(error)

    finally:
        CloseDBConnection()

    return (myresult, err)



def SaveNewOutages(outageList):
    """
    Takes a list of new outage dictionaries and saves them to the database.

    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are saved to the database. All changes are rolled back.
    """
    global mydb
    global mycursor

    err = None

    try:
        OpenDBConnection()

        # SQL INSERT command
        sql = "INSERT INTO " + outageTableName + " ('id', 'json') VALUES (%s, %s);"

        # For each new outage, insert a record for it into the database using the supplied values with the above SQL command
        for outage in outageList:
            values = (outage['id'], json.dumps(outage))
            mycursor.execute(sql, values)
        
        # If INSERT commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = "Failed to insert record to database rollback: {}".format(error)
        # Database rollback because of exception - reverses all executed INSERT commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err



def UpdateOutage(outageUpdateList):
    """
    Takes a list of outage dictionaries that have been updated and saves the updates to the database

    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are updated in the database.
    """
    global mydb
    global mycursor
    
    err = None

    try:
        OpenDBConnection()

        # SQL UPDATE command
        sql = "UPDATE " + outageTableName + " SET 'json' = %s WHERE 'id' = %s;"

        # For each updated outage, update the record for it in the database using the supplied values with the above SQL command
        for outage in outageUpdateList:
            values = (json.dumps(outage), outage['id'])
            mycursor.execute(sql, values)
        
        # If UPDATE commands were successful, permenantly commit the changes.
        mydb.commit()
    
    # If an exception occours while opening a DB connection or accessing the DB
    except mysql.connector.Error as error :
        err = "Failed to insert record to database rollback: {}".format(error)
        # Database rollback because of exception - reverses all executed UPDATE commands so that no changes take effect.
        mydb.rollback()

    finally:
        CloseDBConnection()
    
    return err
