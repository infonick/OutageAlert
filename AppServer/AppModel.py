# Outage Alert
# Application Server
# AppModel
#
# Connects to database for CRUD functionality
# 
# ---------------------------------------------------------------------------------------


#https://pynative.com/python-mysql-transaction-management-using-commit-rollback/


import mysql.connector, json
from mysql.connector import Error
from mysql.connector import errorcode

# Variable definitions
outageTableName = "" #The name of the table that stores records of each power outage
mydb = ''
mycursor = ''
verbose = False #used for verbose output to the terminal for testing purposes. Change to 'True' to see output.


def OpenDBConnection():
    """Opens a new connection to the MySQL Database"""

    global mydb
    global mycursor

    mydb = mysql.connector.connect(
    host="localhost",
    user="yourusername",
    password="yourpassword",
    database="mydatabase"
    )
    
    mydb.autocommit = False
    mycursor = mydb.cursor(dictionary=True)
    if verbose: print("DB connection is open.")
    return (mydb, mycursor)


def CloseDBConnection():
    """Closes an existing database connection"""

    global mydb
    global mycursor

    if(mydb.is_connected()):
        mycursor.close()
        mydb.close()
        if verbose: print("DB connection is closed.")


#(mydb, mycursor) = OpenDBConnection()




def GetOutageList(values):
    """Retrieves all outages from the database that have an id listed in the supplied parameter.
    
    Input: a list of BC Hydro outage id numbers.
    Output: a tuple of two components: 
        The first component is all related database records for outages are retuned as a list of dictionaries.
        The second component is an error message, if applicible.
    """
    myresult = []
    err = None

    try:
        OpenDBConnection()
        
        sql = 'SELECT * FROM ' + outageTableName + " WHERE 'id' IN " + str(tuple(values)) + ";"

        mycursor.execute(sql)

        myresult = mycursor.fetchall()

    except mysql.connector.Error as error :
        err = "Failed to retrieve outage list: {}".format(error)
        # Database rollback because of exception
        mydb.rollback()

    finally:
        CloseDBConnection()

    return (myresult, err)



def SaveNewOutages(outageList):
    """Takes a list of new outage dictionaries and saves them to the database
    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are saved to the database.
    """
    err = None

    try:
        OpenDBConnection()

        sql = "INSERT INTO " + outageTableName + " ('id', 'json') VALUES (%s, %s);"

        for outage in outageList:
            
            values = (outage['id'], json.dumps(outage))
            mycursor.execute(sql, values)
        
        mydb.commit()
    
    except mysql.connector.Error as error :
        err = "Failed to insert record to database rollback: {}".format(error)
        # Database rollback because of exception
        mydb.rollback()


    finally:
        CloseDBConnection()
    
    return err


def UpdateOutage(outageUpdateList):
    """Takes a list of outage dictionaries that have been updated and saves the updates to the database
    Returns a string if any operation was unsuccessful.
    If any operation is unsuccessful, then NO records are updated in the database.
    """
    err = None

    try:
        OpenDBConnection()

        sql = "UPDATE " + outageTableName + " SET 'json' = %s WHERE 'id' = %s;"

        for outage in outageUpdateList:
            
            values = (json.dumps(outage), outage['id'])
            mycursor.execute(sql, values)
        
        mydb.commit()
    
    except mysql.connector.Error as error :
        err = "Failed to insert record to database rollback: {}".format(error)
        # Database rollback because of exception
        mydb.rollback()


    finally:
        CloseDBConnection()
    
    return err