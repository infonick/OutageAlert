# Outage Alert
# Application Server
# test_AppModelDB
#
# This script tests the functions of AppModelDB
#   
# ---------------------------------------------------------------------------------------

import unittest
from test_TestData import *
import AppModelDB as AMDB
import AppTimeLib as ATL




class test_a_DBConnections(unittest.TestCase):
    
    def test_openAndCloseConnection(self):
        self.assertEqual(   AMDB.OpenDBConnection(dbname='TestDB'), 
                            None,
                            "Opening a DB Connection did not work as expected.")

        self.assertEqual(   AMDB.CloseDBConnection(), 
                            None,
                            "Closing a DB Connection did not work as expected.")



class test_b_GetAndSaveOutageList(unittest.TestCase):
    
    def test_GetOutageList1(self):
        (myresult, err) = AMDB.GetOutageList([1,2,0,5,776,780])

        self.assertEqual(   type(myresult),
                            type([]),
                            "Retrieving outages using invalid outage ID numbers did not return a list.")

        self.assertEqual(   len(myresult),
                            0,
                            "Retrieving outages using invalid outage ID numbers returned more than 0 results.")

        self.assertEqual(   AMDB.GetOutageList([]),
                            ([], None),
                            "Retrieving outages using empty outage ID list returned unexpected results.")


    # def test_SaveOutageList2(self):
        currentTime = ATL.GetCurrentUTCTime()

        self.assertEqual(   AMDB.SaveNewOutages([], currentTime),
                            None,
                            "Saving outages using an empty list returned an error.")

        self.assertEqual(   AMDB.SaveNewOutages([TestZone1A], currentTime),
                            None,
                            "Saving outages using a list of 1 outage returned an error.")
        
        self.assertEqual(   AMDB.SaveNewOutages([TestZone2A, TestZone3A], currentTime),
                            None,
                            "Saving outages using a list of 2 outages returned an error.")
        
    
    # def test_UpdateOutage(self)
        currentTime = ATL.GetCurrentUTCTime()

        self.assertEqual(   AMDB.UpdateOutage([TestZone3B], currentTime),
                            None,
                            "Updating an outage using a list of 1 outages returned an error.")
        
        (myresult, err) = AMDB.GetOutageList([777,778,779])

        self.assertEqual(   len(myresult),
                            3,
                            "Retrieving outages using valid outage ID numbers returned an unexpected number of results.")


        




class test_c_GetProperties(unittest.TestCase):
    
    def test_GetProperties(self):
        (myresult, err) = AMDB.GetProperties()

        self.assertEqual(   type(myresult),
                            type([]),
                            "Unexpected type when getting properties.")

        self.assertEqual(   err,
                            None,
                            "Unexpected error when getting properties.") 

        self.assertIn(  TestProp11,
                        myresult, 
                        "TestProp11 not in GetProperties results.")

        self.assertIn(  TestProp21,
                        myresult, 
                        "TestProp21 not in GetProperties results.")

        self.assertIn(  TestProp31,
                        myresult, 
                        "TestProp31 not in GetProperties results.")



class test_d_InsertPropertyOutages(unittest.TestCase):
    
    def test_InsertPropertyOutages(self):
        self.assertEqual(   AMDB.InsertPropertyOutages(TestPropertyOutageList), 
                            None, 
                            "Insertion of Property Outages failed.")
        





class test_e_GetOutageUsersByEmail(unittest.TestCase):

    def test_GetOutageUsersByEmail(self):
        (myresult, err) = AMDB.GetOutageUsersByEmail()

        self.assertEqual(   type(myresult),
                            type([]),
                            "Unexpected type when getting OutageUsersByEmail.")

        self.assertEqual(   err,
                            None,
                            "Unexpected error when getting OutageUsersByEmail.") 

        # self.assertIn(  OutageUserEmail11,
        #                 myresult, 
        #                 "OutageUserEmail11 not in GetOutageUsersByEmail results.")

        # self.assertIn(  OutageUserEmail21,
        #                 myresult, 
        #                 "OutageUserEmail21 not in GetOutageUsersByEmail results.")

        self.assertEqual(   len(myresult),
                            3, 
                            "Number of GetOutageUsersByEmail results is not as expected.")




class test_f_GetOutageUsersByPhone(unittest.TestCase):
    pass




class test_g_UpdatePropertyOutages(unittest.TestCase):
            
     def test_UpdatePropertyOutages(self):
        currentTime = ATL.GetCurrentUTCTime()

        self.assertEqual(   AMDB.UpdateOutage([TestZone3C], currentTime),
                            None,
                            "Updating an outage using a list of 1 outages returned an error.")
        
        self.assertEqual(   AMDB.UpdatePropertyOutages(TestUpdatePropertyOutageList, 0),
                            None,
                            "Updating an outage using a list of 1 outages returned an error.")
        

        (myresult, _) = AMDB.GetOutageUsersByEmail()


        # self.assertIn(  OutageUserEmail11,
        #                 myresult, 
        #                 "OutageUserEmail11 not in GetOutageUsersByEmail results when it should be.")

        # self.assertIn(  OutageUserEmail21,
        #                 myresult, 
        #                 "OutageUserEmail21 not in GetOutageUsersByEmail results when it should be.")

        # self.assertNotIn(   OutageUserEmail31,
        #                     myresult, 
        #                     "OutageUserEmail31 is in GetOutageUsersByEmail results when it should not be.")

        self.assertEqual(  len(myresult),
                        2, 
                        "Number of GetOutageUsersByEmail results is not as expected.")



if __name__ == '__main__':
   
    unittest.main()




    
    # AMDB.OpenDBConnection(dbname='TestDB')



    # try:

        
    #     sql =   "DELETE FROM `Property Outage` WHERE `Property Outage`.`OutageID` = 777 AND `Property Outage`.`PropertyID` = 999999911;"
    #     sql +=  "DELETE FROM `Property Outage` WHERE `Property Outage`.`OutageID` = 778 AND `Property Outage`.`PropertyID` = 999999921;"
    #     sql +=  "DELETE FROM `Property Outage` WHERE `Property Outage`.`OutageID` = 779 AND `Property Outage`.`PropertyID` = 999999931;"
    #     sql +=  "DELETE FROM `Outage` WHERE `Outage`.`OutageID` = 777;"
    #     sql +=  "DELETE FROM `Outage` WHERE `Outage`.`OutageID` = 778;"
    #     sql +=  "DELETE FROM `Outage` WHERE `Outage`.`OutageID` = 779;"

        
    #     # Execute the SQL command
    #     mycursor.execute(sql) 

    #     # Retrieve all of the results
    #     myresult = mycursor.fetchall() 

    # # If an exception occours while opening a DB connection or accessing the DB
    # except mysql.connector.Error as error :
    #     err = f"{error}"

    # finally:
    #     AMDB.CloseDBConnection(), 

