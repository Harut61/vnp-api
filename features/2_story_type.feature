Feature: Manage StoryTypes
  In order to manage StoryTypes
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Story Type News
    Given I am logged in as SuperAdmin
    And I smartly create the StoryType named StoryTypeNews with:
    """
    {
      "title": "News",
      "vneId": "New23423s",
      "vneTitle": "vne345",
      "isActive": true,
      "position": 2,
      "titleForMarker": "markerTitle1",
      "titleForEndUser": "endUserTitle1"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <StoryTypeNews:id>,
       "title": "News",
       "vneId": "New23423s",
      "vneTitle": "vne345",
       "slug": "news",
      "isActive": true,
      "position": 2,
      "titleForMarker": "markerTitle1",
      "titleForEndUser": "endUserTitle1"
    }
    """

  Scenario: Create a Story Type Comedy
    Given I am logged in as SuperAdmin
    And I smartly create the StoryType named StoryTypeComedy with:
    """
    {
      "title": "Comedy",
      "vneId": "N23423s",
      "vneTitle": "v345",
      "isActive": true,
      "position": 3,
      "titleForMarker": "markerTitle2",
      "titleForEndUser": "endUserTitle2"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <StoryTypeComedy:id>,
       "title": "Comedy",
       "vneId": "N23423s",
      "vneTitle": "v345",
       "slug": "comedy",
      "isActive": true,
      "position": 3,
      "titleForMarker": "markerTitle2",
      "titleForEndUser": "endUserTitle2"
    }
    """

  Scenario: Create a Story Type Entertainment
    Given I am logged in as SuperAdmin
    And I smartly create the StoryType named StoryTypeEntertainment with:
    """
    {
      "title": "Entertainment News",
      "vneId": "N2342",
      "vneTitle": "vn345",
      "isActive": true,
      "position": 1,
      "titleForMarker": "markerTitle3",
      "titleForEndUser": "endUserTitle3"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <StoryTypeEntertainment:id>,
       "title": "Entertainment News",
       "vneId": "N2342",
      "vneTitle": "vn345",
       "slug": "entertainment-news",
      "isActive": true,
      "position": 1,
      "titleForMarker": "markerTitle3",
      "titleForEndUser": "endUserTitle3"
    }
    """

  Scenario: Create a Story Type NULL Title
    Given I am logged in as SuperAdmin
    And I smartly create the StoryType named StoryTypeNullTitle with:
    """
    {
      "vneId": "N2342",
      "vneTitle": "vn345",
      "isActive": true,
      "position": 1,
      "titleForMarker": "markerTitle4",
      "titleForEndUser": "endUserTitle4"
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
            "propertyPath": "title",
            "message": "This value should not be null."
          }
        ]
      }
    """

  Scenario: Create a Story Type Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the StoryType named StoryTypeNullTitle with:
    """
    {
    "vneId": "N2342",
      "vneTitle": "vn345",
      "title": "",
      "isActive": true,
      "position": 1,
      "titleForMarker": "markerTitle5",
      "titleForEndUser": "endUserTitle5"
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
            "propertyPath": "title",
            "message": "This value should not be blank."
          }
        ]
      }
    """

  Scenario: Create a Story Type Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the StoryType named StoryTypeNullTitle with:
    """
    {
    "vneId": "N2342",
      "vneTitle": "vn345",
      "title": "",
      "isActive": true,
      "position": 1,
      "titleForMarker": "markerTitle6",
      "titleForEndUser": "endUserTitle6"
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
            "propertyPath": "title",
            "message": "This value should not be blank."
          }
        ]
      }
    """

  Scenario: Get all Story Types Order By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeComedy:id>,
          "title": "Comedy",
          "slug": "comedy",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <StoryTypeNews:id>,
          "title": "News",
          "slug": "news",
          "isActive": true,
          "position": 2
      },
      {
          "id" : <StoryTypeEntertainment:id>,
          "title": "Entertainment News",
          "slug": "entertainment-news",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Position ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types?order[position]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeEntertainment:id>,
          "title": "Entertainment News",
          "slug": "entertainment-news",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <StoryTypeNews:id>,
          "title": "News",
          "slug": "news",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <StoryTypeComedy:id>,
          "title": "Comedy",
          "slug": "comedy",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Get all Story Types Order By Position DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types?order[position]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeComedy:id>,
          "title": "Comedy",
          "slug": "comedy",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <StoryTypeNews:id>,
          "title": "News",
          "slug": "news",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <StoryTypeEntertainment:id>,
          "title": "Entertainment News",
          "slug": "entertainment-news",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Title ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types?order[title]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeComedy:id>,
          "title": "Comedy",
          "slug": "comedy",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <StoryTypeEntertainment:id>,
          "title": "Entertainment News",
          "slug": "entertainment-news",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <StoryTypeNews:id>,
          "title": "News",
          "slug": "news",
          "isActive": true,
          "position": 2
       }
    ]
    """

  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types?order[title]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeNews:id>,
          "title": "News",
          "slug": "news",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <StoryTypeEntertainment:id>,
          "title": "Entertainment News",
          "slug": "entertainment-news",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <StoryTypeComedy:id>,
          "title": "Comedy",
          "slug": "comedy",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Delete an Story Type
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/story_types/<StoryTypeNews:id>"
    Then the response status code should be 204


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types?order[title]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeEntertainment:id>,
          "title": "Entertainment News",
          "slug": "entertainment-news",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <StoryTypeComedy:id>,
          "title": "Comedy",
          "slug": "comedy",
          "isActive": true,
          "position": 3
      }
    ]
    """


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_types?trash=true"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <StoryTypeNews:id>,
          "title": "News",
          "slug": "news",
          "isActive": true,
          "position": 2
       }
    ]
    """
