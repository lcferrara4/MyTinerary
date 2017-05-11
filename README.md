# MyTinerary
Website for Generating Personalized Travel Itineraries

* Description of Project:
  This application automatically generates travel suggestions based on user preferences and reviews and then gives the user a drag and drop calendar on which they can build their travel itinerary.

* Usefulness:
  Our website will be helpful to plan trips quickly, filling in events based on the user's preferences for cost and activities. It will take much of the hassle out of looking through multiple websites and travel books to decide what to do in a new area (specifically implemented for Chicago right now). Unlike existing websites, this site doesn't have preplanned itineraries. Instead, it takes into account what the user would want to do.

* Relational Schema:
  Users(email, password, first_name, last_name)
  Event(event_id, start_time, end_time, site_id, calendar_id)
  Calendar(calendar_id, title, trip_start, trip_end, creator_email)
  Permissions(calendar_id, user_email)
  Site(place_id, name, category, open_time, close_time, rating, price, food, bar, sightseeing, shopping, art, museum, theater, sports, late, lon, addr)
  Preferences(email, food, bar, sightseeing, shopping, art, museum, theater, sports, one_dollar, two_dollar, three_dollar) Suggestions(email, general_category, site_id, site_name, score)

* Collection of Data:
  We got most of our data from the Google Places API. It provided us with the name, location, category, hours of operation, ratings, and price for any notable locations in Chicago, New York, and San Francisco. The data from New York and San Francisco was used as training data for our Naive Bayes classifier, which was used to classify the locations taken from Chicago. We also added some data by registering fake users and setting up calendars and preferences for them.

* List of Functionalities:
  Creating account
  Login/Logout
  Update password
  Update preferences
  Create new calendar
  Drag and drop calendar
  View upcoming or prior calendars Save calendar
  Share or delete calendar

* Explanation of Advanced Functions:

1. Site Classification

  When a user goes to the dashboard page, we check if there are any entries in the Site table that have NULL values for the food category. If they do, these sites have not yet been categorized. These are added to a new user specific file, in a format that has '|' separated fields including the place_id, name, category, and rating of the site as well as 8 fields that are 0 by default. This file is then run as the input of a python script that does Naive Bayes classification of the sites.

  The Naive Bayes Classifier works by finding the counts of words in the names and Google-given category for sites from a set of training data (taken from sites in NY and San Francisco and categorized by hand) and using these counts to determine the probability that a site is each of the 8 categories based on that site's name and Google-given category. Although this function is working fairly well, it could be improved by including more training data (right now, there are mistakes like "The Art of Shaving" being classified as an art exhibit) and possibly by extending to bigrams and trigrams.

  For each Site sent to the script, a line is output with the place_id and a '|' separated listing of the true values for the 8 fields (food, bars, sightseeing, shopping, art, museum, theater, sports). These are then used to update those categories in the Site table for the corresponding place_id.

2. Suggestion Scoring

  Suggestion scores are calculated with the default values for preferences when a user creates an account and more importantly everytime the user updates his/her preferences. To update the suggestions, first any existing entries in the Suggestions table for that User are deleted.

  The scores are calculated by taking email and the 11 user preferences, the 8 mentioned above plus 3 for pricing, from the Preferences table and place_id, name, rating, price, and the 8 categorizations from the Site table. Then the "sum_of_matches" is calculate by adding instances for the 8 corresponding categories in which both are 1 (sum to 2), the price flags match appropriately, and when the price flag is zero (non-classified). Some tweaking was done to make these results optimized. The power of the food and bar categories was halved, because a lot of sports bars or "bar and grill"s get classified as barsand restaurants, so we don't want them to be double counted. This also helped just with the sheer number of restaurants that would be suggested due to quantity in the database. The shopping category was multipied by 7/8 to reduce the number of tiny shops that would show up in results. Finally, 0.5 was added to the sum_of_matches to eliminate the possibility of a 0, which would later mess up the calculation.

  This sum_of_matches is then multiplied by the rating of the site + 1 (b/c unranked sites are given as 0, and we don't want to eliminate them entirely). The output is the score of the site that goes into the suggestions table for that user. Then, dependent on the days of the trip selected in the calendar a limited number of sites from the suggestions table for that user sorted in descending order of score are recommended on the side bar of the Calendar page.
