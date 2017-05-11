#from googleplaces import GooglePlaces, types, lang
import urllib.request, json

MyKey = 'AIzaSyAPD2VInRpjn4LKAQAcdLflikswQjW_IoU'

#google_places = GooglePlaces(YOUR_API_KEY)


#Grabbing and parsing the JSON data
def GoogPlac(lat,lng,radius, types, key):
  #making the url
  AUTH_KEY = key
  LOCATION = str(lat) + "," + str(lng)
  RADIUS = radius
  TYPE = types
  MyUrl = ('https://maps.googleapis.com/maps/api/place/nearbysearch/json'
           '?location=%s'
           '&radius=%s'
           '&types=%s'
           '&sensor=false&key=%s') % (LOCATION, RADIUS, TYPE, AUTH_KEY)
  #grabbing the JSON result
  response = urllib.request.urlopen(MyUrl)
  jsonRaw = response.read()
  output = jsonRaw.decode('utf-8')
  jsonData = json.loads(output)
  return jsonData

#This is a helper to grab the Json data that I want in a list
def IterJson(place):
  x = [place['place_id'], place['name'], place['types']]
  return x


new_file = open('train.txt', 'a')
search = GoogPlac(lat=37.769,lng=-122.466,radius=500, types='aquarium', key=MyKey)
if search['status'] == 'OK':
    for place in search['results']:
        x = IterJson(place)
        new_file.write(str(x[0]) + '|' + str(x[1]) + '|' + str(x[2][0]) + '|NULL|0|0|0|0|0|0|0|0\n')
