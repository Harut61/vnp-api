Feature: Manage Shows
  In order to manage Shows
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Show Nightline
    Given I am logged in as SuperAdmin
    And I smartly create the Show named ShowNightline with:
    """
    {
      "title": "Nightline",
      "isActive": true,
      "position": 2
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <ShowNightline:id>,
       "title": "Nightline",
       "slug": "nightline",
      "isActive": true,
      "position": 2
    }
    """

  Scenario: Create a Show CBS This Morning
    Given I am logged in as SuperAdmin
    And I smartly create the Show named ShowCBSThisMorning with:
    """
    {
      "title": "CBS This Morning",
      "isActive": true,
      "position": 3
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <ShowCBSThisMorning:id>,
       "title": "CBS This Morning",
       "slug": "cbs-this-morning",
      "isActive": true,
      "position": 3
    }
    """

  Scenario: Create a Show Entertainment
    Given I am logged in as SuperAdmin
    And I smartly create the Show named ShowAbc7News with:
    """
    {
      "title": "ABC7 News 6 AM",
      "isActive": true,
      "position": 1
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <ShowAbc7News:id>,
       "title": "ABC7 News 6 AM",
       "slug": "abc7-news-6-am",
      "isActive": true,
      "position": 1
    }
    """

  Scenario: Create a Show NULL Title
    Given I am logged in as SuperAdmin
    And I smartly create the Show named ShowNullTitle with:
    """
    {
      "isActive": true,
      "position": 1
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

  Scenario: Create a Show Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the Show named ShowNullTitle with:
    """
    {
      "title": "",
      "isActive": true,
      "position": 1
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

  Scenario: Create a Show Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the Show named ShowNullTitle with:
    """
    {
      "title": "",
      "isActive": true,
      "position": 1
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
    And I send a smart "Get" request to "/shows"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowCBSThisMorning:id>,
          "title": "CBS This Morning",
          "slug": "cbs-this-morning",
          "isActive": true,
          "position": 3
      },
       {
          "id" : <ShowNightline:id>,
          "title": "Nightline",
          "slug": "nightline",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <ShowAbc7News:id>,
          "title": "ABC7 News 6 AM",
          "slug": "abc7-news-6-am",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Position ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/shows?order[position]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowAbc7News:id>,
          "title": "ABC7 News 6 AM",
          "slug": "abc7-news-6-am",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <ShowNightline:id>,
          "title": "Nightline",
          "slug": "nightline",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <ShowCBSThisMorning:id>,
          "title": "CBS This Morning",
          "slug": "cbs-this-morning",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Get all Story Types Order By Position DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/shows?order[position]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowCBSThisMorning:id>,
          "title": "CBS This Morning",
          "slug": "cbs-this-morning",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <ShowNightline:id>,
          "title": "Nightline",
          "slug": "nightline",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <ShowAbc7News:id>,
          "title": "ABC7 News 6 AM",
          "slug": "abc7-news-6-am",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Title ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/shows?order[title]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowAbc7News:id>,
          "title": "ABC7 News 6 AM",
          "slug": "abc7-news-6-am",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <ShowCBSThisMorning:id>,
          "title": "CBS This Morning",
          "slug": "cbs-this-morning",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <ShowNightline:id>,
          "title": "Nightline",
          "slug": "nightline",
          "isActive": true,
          "position": 2
       }
    ]
    """

  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/shows?order[title]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowNightline:id>,
          "title": "Nightline",
          "slug": "nightline",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <ShowCBSThisMorning:id>,
          "title": "CBS This Morning",
          "slug": "cbs-this-morning",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <ShowAbc7News:id>,
          "title": "ABC7 News 6 AM",
          "slug": "abc7-news-6-am",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Delete an Show
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/shows/<ShowNightline:id>"
    Then the response status code should be 204


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/shows?order[title]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowCBSThisMorning:id>,
          "title": "CBS This Morning",
          "slug": "cbs-this-morning",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <ShowAbc7News:id>,
          "title": "ABC7 News 6 AM",
          "slug": "abc7-news-6-am",
          "isActive": true,
          "position": 1
      }
    ]
    """


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/shows?trash=true"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <ShowNightline:id>,
          "title": "Nightline",
          "slug": "nightline",
          "isActive": true,
          "position": 2
       }
    ]
    """
