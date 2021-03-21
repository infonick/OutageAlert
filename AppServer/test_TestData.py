# Outage Alert
# Application Server
# test_TestData
#
# This file contains data for testing
#   
# ---------------------------------------------------------------------------------------

TestZone1A = {   'id':                      777, 
                'gisId':                    77, 
                'regionId':                 7, 
                'municipality':             'Testville 1', 
                'area':                     '1 Testing St', 
                'cause':                    'Cause of Test Outage 1', 
                'numCustomersOut':          0, 
                'crewStatusDescription':    'Crew assigned', 
                'crewEta':      None, 
                'dateOff':      1616267460000, 
                'dateOn':       None, 
                'lastUpdated':  1616268013000, 
                'regionName':   'Test Municipality 1', 
                'crewEtr':      None, 
                'showEta':      False, 
                'showEtr':      False, 
                'latitude':     48.8022169, 
                'longitude':    -127.1918677,
                'polygon':      [-127.6918677, 48.3022169, -127.6918677, 49.3022169, -126.6918677, 49.3022169, -126.6918677, 48.3022169]
            }

TestZone2A = {   'id':                      778, 
                'gisId':                    78, 
                'regionId':                 8, 
                'municipality':             'Testville 2', 
                'area':                     '2 Testing St', 
                'cause':                    'Cause of Test Outage 2', 
                'numCustomersOut':          0, 
                'crewStatusDescription':    'Crew assigned', 
                'crewEta':      None, 
                'dateOff':      1616267460000, 
                'dateOn':       None, 
                'lastUpdated':  1616268013000, 
                'regionName':   'Test Municipality 2', 
                'crewEtr':      None, 
                'showEta':      False, 
                'showEtr':      False, 
                'latitude':     48.8022169, 
                'longitude':    -130.1918677,
                'polygon':      [-129.6918677, 49.3022169, -129.6918677, 48.3022169, -130.6918677, 48.3022169, -130.6918677, 49.3022169]
            }

TestZone3A = {  'id':                       779, 
                'gisId':                    79,
                'regionId':                 9, 
                'municipality':             'Testville 3', 
                'area':                     '3 Testing St', 
                'cause':                    'Cause of Test Outage 3', 
                'numCustomersOut':          0, 
                'crewStatusDescription':    'Crew assigned', 
                'crewEta':      None, 
                'dateOff':      1616267460000, 
                'dateOn':       None, 
                'lastUpdated':  1616268013000, 
                'regionName':   'Test Municipality 3', 
                'crewEtr':      None, 
                'showEta':      False, 
                'showEtr':      False, 
                'latitude':     48.8022169, 
                'longitude':    -132.1918677,
                'polygon':      [-131.6918677, 49.3022169, -131.6918677, 48.3022169, -132.6918677, 48.3022169, -132.6918677, 49.3022169]
            }

TestZone3B = {  'id':                       779, 
                'gisId':                    79,
                'regionId':                 9, 
                'municipality':             'Testville 3', 
                'area':                     '3 Testing St', 
                'cause':                    'Solution of Test Outage 3', # previously 'Cause of Test Outage 3', 
                'numCustomersOut':          0, 
                'crewStatusDescription':    'Crew On-Site', # previously 'Crew assigned'
                'crewEta':      None, 
                'dateOff':      1616267460000, 
                'dateOn':       None, 
                'lastUpdated':  1616268013000, 
                'regionName':   'Test Municipality 3', 
                'crewEtr':      None, 
                'showEta':      False, 
                'showEtr':      False, 
                'latitude':     48.8022169, 
                'longitude':    -132.1918677,
                'polygon':      [-131.6918677, 49.3022169, -131.6918677, 48.3022169, -132.6918677, 48.3022169, -132.6918677, 49.3022169]
            }

TestZone3C = {  'id':                       779, 
                'gisId':                    79,
                'regionId':                 9, 
                'municipality':             'Testville 3', 
                'area':                     '3 Testing St', 
                'cause':                    'Solution of Test Outage 3', # previously 'Cause of Test Outage 3', 
                'numCustomersOut':          0, 
                'crewStatusDescription':    'Crew On-Site', # previously 'Crew assigned'
                'crewEta':      None, 
                'dateOff':      1616267460000, 
                'dateOn':       1616277460000, # previously 'None'
                'lastUpdated':  1616277470000, # previously '1616268013000'
                'regionName':   'Test Municipality 3', 
                'crewEtr':      None, 
                'showEta':      False, 
                'showEtr':      False, 
                'latitude':     48.8022169, 
                'longitude':    -132.1918677,
                'polygon':      [-131.6918677, 49.3022169, -131.6918677, 48.3022169, -132.6918677, 48.3022169, -132.6918677, 49.3022169]
            }


TestPropertyOutageList = [  {'outageID': 777, 'propertyID': 999999911},
                            {'outageID': 778, 'propertyID': 999999921},
                            {'outageID': 779, 'propertyID': 999999931}]

TestUpdatePropertyOutageList = [{'outageID': 779, 'propertyID': 999999931}]


TestProp11 = {  'PropertyID':       999999911,
                'Property Name':    'TestProp1 Outage1',
                'Address':          '1 Test1 St',
                'AccountID':        888888811,
                'Latitude':         48.8022169, 
                'Longitude':        -127.1918677
                }

TestProp21 = {  'PropertyID':       999999921,
                'Property Name':    'TestProp1 Outage2',
                'Address':          '1 Test2 St',
                'AccountID':        888888821,
                'Latitude':         48.8022169, 
                'Longitude':        -130.1918677
                }
            
TestProp31 = {  'PropertyID':       999999931,
                'Property Name':    'TestProp1 Outage3',
                'Address':          '1 Test3 St',
                'AccountID':        888888831,
                'Latitude':         48.8022169, 
                'Longitude':        -132.1918677
                }




# SQL CODE TO ADD TEST DATA to DB:
# for prop in [TestProp11, TestProp21, TestProp31]:
#     print(f"INSERT INTO `Property` (`PropertyID`, `Property Name`, `Address`, `AccountID`, `Latitude`, `Longitude`) VALUES ('{prop['PropertyID']}', '{prop['Property Name']}', '{prop['Address']}', '{prop['AccountID']}', '{prop['Latitude']}', '{prop['Longitude']}')") 

# INSERT INTO `Account` (`AccountID`, `Password`, `Email`, `Created Date`, `Locked`) VALUES ('888888811', 'None', '1@two.ca', '2021-03-14', '0');
# INSERT INTO `Account` (`AccountID`, `Password`, `Email`, `Created Date`, `Locked`) VALUES ('888888821', 'None', '2@two.ca', '2021-03-14', '0');
# INSERT INTO `Account` (`AccountID`, `Password`, `Email`, `Created Date`, `Locked`) VALUES ('888888831', 'None', '3@two.ca', '2021-03-14', '0');

# INSERT INTO `Property` (`PropertyID`, `Property Name`, `Address`, `AccountID`, `Latitude`, `Longitude`) VALUES ('999999911', 'TestProp1 Outage1', '1 Test1 St', '888888811', '48.8022169', '-127.1918677');
# INSERT INTO `Property` (`PropertyID`, `Property Name`, `Address`, `AccountID`, `Latitude`, `Longitude`) VALUES ('999999921', 'TestProp1 Outage2', '1 Test2 St', '888888821', '48.8022169', '-130.1918677');
# INSERT INTO `Property` (`PropertyID`, `Property Name`, `Address`, `AccountID`, `Latitude`, `Longitude`) VALUES ('999999931', 'TestProp1 Outage3', '1 Test3 St', '888888831', '48.8022169', '-132.1918677');

# INSERT INTO `Recipients` (`Name`, `Phone`, `Contact Email`, `AccountID`) VALUES ('TestName1', '0000000000', 'test@test.test', '888888811');
# INSERT INTO `Recipients` (`Name`, `Phone`, `Contact Email`, `AccountID`) VALUES ('TestName2', '0000000000', 'test@test.test', '888888821');
# INSERT INTO `Recipients` (`Name`, `Phone`, `Contact Email`, `AccountID`) VALUES ('TestName3', '0000000000', 'test@test.test', '888888831');

# INSERT INTO `Recipient Properties` (`AccountID`, `Name`, `PropertyID`, `Active`) VALUES ('888888811', 'TestName1', '999999911', '1');
# INSERT INTO `Recipient Properties` (`AccountID`, `Name`, `PropertyID`, `Active`) VALUES ('888888821', 'TestName2', '999999921', '1');
# INSERT INTO `Recipient Properties` (`AccountID`, `Name`, `PropertyID`, `Active`) VALUES ('888888831', 'TestName3', '999999931', '1');

OutageUserEmail11 = {   'PropertyID': 888888811, 
                        'Property Name': 'TestProp1 Outage1', 
                        'Address': '1 Test1 St', 
                        'Name': 'TestName1', 
                        'Contact Email': 'test@test.test'
                    }
OutageUserEmail21 = {   'PropertyID': 888888821, 
                        'Property Name': 'TestProp1 Outage2', 
                        'Address': '1 Test2 St', 
                        'Name': 'TestName2', 
                        'Contact Email': 'test@test.test'
                    }
OutageUserEmail31 = {   'PropertyID': 888888831, 
                        'Property Name': 'TestProp1 Outage3', 
                        'Address': '1 Test3 St', 
                        'Name': 'TestName3', 
                        'Contact Email': 'test@test.test'
                    }








#TEST DATA FOR AppModelOutageMessages
