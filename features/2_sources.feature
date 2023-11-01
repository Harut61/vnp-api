Feature: Manage HighLevelSubjectTags
  In order to manage HighLevelSubjectTags
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Source CBS Network
    Given I am logged in as SuperAdmin
    And I smartly create the Source named SourceCBSNetwork with:
    """
    {
      "title": "CBS Network",
      "isActive": true,
      "position": 2
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <SourceCBSNetwork:id>,
       "title": "CBS Network",
       "slug": "cbs-network",
      "isActive": true,
      "position": 2
    }
    """

  Scenario: Create a Source ABC Network
    Given I am logged in as SuperAdmin
    And I smartly create the Source named SourceABCNetwork with:
    """
    {
      "title": "ABC Network",
      "isActive": true,
      "position": 3
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <SourceABCNetwork:id>,
       "title": "ABC Network",
       "slug": "abc-network",
      "isActive": true,
      "position": 3
    }
    """

  Scenario: Create a Source Entertainment
    Given I am logged in as SuperAdmin
    And I smartly create the Source named SourceKRON4 with:
    """
    {
      "title": "KRON 4",
      "isActive": true,
      "position": 1
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <SourceKRON4:id>,
       "title": "KRON 4",
       "slug": "kron-4",
      "isActive": true,
      "position": 1
    }
    """

  Scenario: Create a Source NULL Title
    Given I am logged in as SuperAdmin
    And I smartly create the Source named HighLevelSubjectTagNullTitle with:
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

  Scenario: Create a Source Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the Source named HighLevelSubjectTagNullTitle with:
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

  Scenario: Create a Source Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the Source named HighLevelSubjectTagNullTitle with:
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
    And I send a smart "Get" request to "/sources"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceABCNetwork:id>,
          "title": "ABC Network",
          "slug": "abc-network",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <SourceCBSNetwork:id>,
          "title": "CBS Network",
          "slug": "cbs-network",
          "isActive": true,
          "position": 2
      },
      {
          "id" : <SourceKRON4:id>,
          "title": "KRON 4",
          "slug": "kron-4",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Position ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sources?order[position]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceKRON4:id>,
          "title": "KRON 4",
          "slug": "kron-4",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <SourceCBSNetwork:id>,
          "title": "CBS Network",
          "slug": "cbs-network",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <SourceABCNetwork:id>,
          "title": "ABC Network",
          "slug": "abc-network",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Get all Story Types Order By Position DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sources?order[position]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceABCNetwork:id>,
          "title": "ABC Network",
          "slug": "abc-network",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <SourceCBSNetwork:id>,
          "title": "CBS Network",
          "slug": "cbs-network",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <SourceKRON4:id>,
          "title": "KRON 4",
          "slug": "kron-4",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Title ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sources?order[title]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceABCNetwork:id>,
          "title": "ABC Network",
          "slug": "abc-network",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <SourceCBSNetwork:id>,
          "title": "CBS Network",
          "slug": "cbs-network",
          "isActive": true,
          "position": 2
       },
       {
          "id" : <SourceKRON4:id>,
          "title": "KRON 4",
          "slug": "kron-4",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sources?order[title]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceKRON4:id>,
          "title": "KRON 4",
          "slug": "kron-4",
          "isActive": true,
          "position": 1
      },
        {
          "id" : <SourceCBSNetwork:id>,
          "title": "CBS Network",
          "slug": "cbs-network",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <SourceABCNetwork:id>,
          "title": "ABC Network",
          "slug": "abc-network",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Delete an Source
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/sources/<SourceCBSNetwork:id>"
    Then the response status code should be 204


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sources?order[title]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceKRON4:id>,
          "title": "KRON 4",
          "slug": "kron-4",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <SourceABCNetwork:id>,
          "title": "ABC Network",
          "slug": "abc-network",
          "isActive": true,
          "position": 3
      }
    ]
    """


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sources?trash=true"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <SourceCBSNetwork:id>,
          "title": "CBS Network",
          "slug": "cbs-network",
          "isActive": true,
          "position": 2
       }
    ]
    """
