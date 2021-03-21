# Outage Alert
# Application Server
# test_AppModelGetOutages
#
# This script tests the functions of AppModelGetOutages
#   
# ---------------------------------------------------------------------------------------

import unittest
import AppTimeLib
import AppModelGetOutages as AMGO

class test_RetrieveBCHydro(unittest.TestCase):

    outageKeys =   ['id', 
                    'gisId', 
                    'regionId', 
                    'municipality', 
                    'area', 
                    'cause', 
                    'numCustomersOut', 
                    'crewStatusDescription', 
                    'crewEta', 
                    'dateOff', 
                    'dateOn', 
                    'lastUpdated', 
                    'regionName', 
                    'crewEtr', 
                    'showEta', 
                    'showEtr', 
                    'latitude', 
                    'longitude', 
                    'polygon']


    def test_RetrieveBCHydro_Output(self):
        self.assertEqual(type(AMGO.RetrieveBCHydro()), type((0,0)))
            
        (outputOutages, outputTime) = AMGO.RetrieveBCHydro()

        self.assertEquals(  list(outputOutages[0].keys()), 
                            self.outageKeys, 
                            "Dictionary keys of the retreived outages did not match expected pattern.")

        self.assertEquals(  str(type(outputTime)), 
                            "<class 'datetime.datetime'>", 
                            "datetime type not as epxected.")
        
        # TODO: assert that time of json retreival is reasonably recent.
        # self.assert




class test_SortOutages(unittest.TestCase):
    pass
    # return (newOutages, existingOutages, dbOutages)




class test_DeactivateOutages(unittest.TestCase):
    pass



if __name__ == '__main__':
    unittest.main()
    # (outputOutages, outputTime) = AMGO.RetrieveBCHydro()
    # print (outputOutages[0], "\n\n", outputOutages[1])


# {'id':                      777, 
# 'gisId':                    77, 
# 'regionId':                 7, 
# 'municipality':             'Testville', 
# 'area':                     '123 Testing St', 
# 'cause':                    'Cause of Test Outage', 
# 'numCustomersOut':          0, 
# 'crewStatusDescription':    'Crew assigned', 
# 'crewEta':      None, 
# 'dateOff':      1616267460000, 
# 'dateOn':       None, 
# 'lastUpdated':  1616268013000, 
# 'regionName':   'Test Municipality', 
# 'crewEtr':      None, 
# 'showEta':      False, 
# 'showEtr':      False, 
# 'latitude':     48.8022169, 
# 'longitude':    -127.1918677,
# 'polygon':      [-127.6918677, 49.3022169, -127.6918677, 49.3022169, -126.6918677, 48.3022169, -126.6918677, 48.3022169]}
