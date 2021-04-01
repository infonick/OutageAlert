# Outage Alert
# Application Server
# AppModelPolygonPointFunctions
#
# This script has functions for ploygons and coordinates
#   
# ---------------------------------------------------------------------------------------
  
import shapely
from shapely.geometry import Point, Polygon


def ShapelyPolygon (googleMapsPloygon):
    shapelyPolygon = []

    for i in range(0, len(googleMapsPloygon),2):
        shapelyPolygon.append((googleMapsPloygon[i], googleMapsPloygon[i+1]))
    
    return Polygon(shapelyPolygon)



def PointInPolygon (latitude, longitude, shapelyPolygon):
    thePoly = ShapelyPolygon(shapelyPolygon)
    return (thePoly.contains(Point(longitude, latitude)))



def TestPointInPolygonFunction():

    TESTPOLYGON = [-122.378783, 49.022714, -122.377991, 49.023648, -122.371488, 49.03132, -122.371386, 49.031473, 
            -122.371328, 49.031635, -122.370883, 49.033758, -122.370872, 49.033933, -122.370914, 49.034106, -122.371005, 
            49.034271, -122.371144, 49.03442, -122.376513, 49.039123, -122.376661, 49.039233, -122.376834, 49.039326, 
            -122.377028, 49.039398, -122.377237, 49.039449, -122.3781, 49.039608, -122.37836, 49.039639, -122.378625, 49.039636, 
            -122.378883, 49.039599, -122.379126, 49.039531, -122.379344, 49.039433, -122.37953, 49.039309, -122.379676, 49.039164, 
            -122.379776, 49.039003, -122.38013, 49.038233, -122.380171, 49.038116, -122.383257, 49.025525, -122.383273, 49.025354, 
            -122.383184, 49.023514, -122.383155, 49.023358, -122.383086, 49.023207, -122.382978, 49.023067, -122.382834, 49.022941, 
            -122.382659, 49.022834, -122.382458, 49.022749, -122.382238, 49.022688, -122.380395, 49.022297, -122.380134, 49.02226, 
            -122.379866, 49.022257, -122.379603, 49.022288, -122.379354, 49.022352, -122.379128, 49.022448, -122.378936, 49.02257]

    # 'latitude': 49.031038, 
    # 'longitude': -122.377685, 
    # TESTPOINT = [-122.377685, 49.031038]
    lat = 49.031038
    lon = -122.377685


    # sPoint1 = Point(myPoint[0], myPoint[1])
    # sPoint2 = Point(tuple(myPoint))

    # print(sPoint1.x, sPoint1.y)
    # print(len(myPolygon))
    myPoly = Polygon(TESTPOLYGON)
    print (f"Test passes: {PointInPolygon(lat, lon, myPoly)}")


