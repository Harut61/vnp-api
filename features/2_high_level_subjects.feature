Feature: Manage HighLevelSubjectTags
  In order to manage HighLevelSubjectTags
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a High Level Subject Tag PO
    Given I am logged in as SuperAdmin
    And I smartly create the HighLevelSubjectTag named HighLevelSubjectTagNewsPo with:
    """
    {
      "vneId" : "hass123213",
      "vneTitle": "vneTitle",
      "titleForMarker": "PO",
      "titleForEndUser": "PO",
      "isActive": true,
      "position": 2
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id" : <HighLevelSubjectTagNewsPo:id>,
        "vneId" : "hass123213",
        "vneTitle": "vneTitle",
        "titleForMarker": "PO",
        "titleForEndUser": "PO",
        "isActive": true,
        "position": 2
    }
    """

  Scenario: Create a High Level Subject Tag JCL
    Given I am logged in as SuperAdmin
    And I smartly create the HighLevelSubjectTag named HighLevelSubjectTagNewsJcl with:
    """
    {
      "vneId" : "ha213",
      "vneTitle": "vneTitle1",
      "titleForMarker": "JCL",
      "titleForEndUser": "JCL",
      "isActive": true,
      "position": 3
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id" : <HighLevelSubjectTagNewsJcl:id>,
        "vneId" : "ha213",
        "vneTitle": "vneTitle1",
        "titleForMarker": "JCL",
        "titleForMarker": "JCL",
        "slug": "jcl",
        "isActive": true,
        "position": 3
    }
    """

  Scenario: Create a High Level Subject Tag Entertainment
    Given I am logged in as SuperAdmin
    And I smartly create the HighLevelSubjectTag named HighLevelSubjectTagOT with:
    """
    {
      "vneId" : "ha23213",
      "vneTitle": "vneTitle2",
      "titleForMarker": "OT",
      "titleForEndUser": "OT",
      "isActive": true,
      "position": 1
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <HighLevelSubjectTagOT:id>,
      "vneId" : "ha23213",
      "vneTitle": "vneTitle2",
      "titleForMarker": "OT",
      "titleForEndUser": "OT",
       "slug": "ot",
      "isActive": true,
      "position": 1
    }
    """

  Scenario: Create a High Level Subject Tag NULL titleForMarker
    Given I am logged in as SuperAdmin
    And I smartly create the HighLevelSubjectTag named HighLevelSubjectTagNulltitleForMarker with:
    """
    {
      "vneId" : "ha23",
      "vneTitle": "vneTitle5",
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
            "propertyPath": "titleForMarker",
            "message": "This value should not be null."
          }
        ]
      }
    """

  Scenario: Create a High Level Subject Tag Empty vneId
    Given I am logged in as SuperAdmin
    And I smartly create the HighLevelSubjectTag named HighLevelSubjectTagNullVneId with:
    """
    {
      "vneTitle": "vneTitle2",
      "titleForMarker": "AS",
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
            "propertyPath": "vneId",
            "message": "This value should not be null."
          }
        ]
      }
    """

  Scenario: Create a High Level Subject Tag Empty titleForMarker
    Given I am logged in as SuperAdmin
    And I smartly create the HighLevelSubjectTag named HighLevelSubjectTagNulltitleForMarker with:
    """
    {
      "vneId" : "ha23",
      "vneTitle": "vneTitle2",
      "titleForMarker": "",
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
            "propertyPath": "titleForMarker",
            "message": "This value should not be blank."
          }
        ]
      }
    """

  

  Scenario: Get all Story Types Order By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
          {
          "id" : <HighLevelSubjectTagNewsJcl:id>,
          "titleForMarker": "JCL",
          "titleForMarker": "JCL",
          "slug": "jcl",
          "isActive": true,
          "position": 3
      },
       {
          "id" : <HighLevelSubjectTagNewsPo:id>,
          "titleForMarker": "PO",
          "titleForEndUser": "PO",
          "slug": "po",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <HighLevelSubjectTagOT:id>,
          "titleForMarker": "OT",
          "titleForMarker": "OT",
          "slug": "ot",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By Position ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags?order[position]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <HighLevelSubjectTagOT:id>,
          "titleForMarker": "OT",
          "titleForMarker": "OT",
          "slug": "ot",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <HighLevelSubjectTagNewsPo:id>,
          "titleForMarker": "PO",
          "titleForEndUser": "PO",
          "slug": "po",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <HighLevelSubjectTagNewsJcl:id>,
          "titleForMarker": "JCL",
          "titleForMarker": "JCL",
          "slug": "jcl",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Get all Story Types Order By Position DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags?order[position]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <HighLevelSubjectTagNewsJcl:id>,
          "titleForMarker": "JCL",
          "titleForEndUser": "JCL",
          "slug": "jcl",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <HighLevelSubjectTagNewsPo:id>,
          "titleForMarker": "PO",
          "titleForEndUser": "PO",
          "slug": "po",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <HighLevelSubjectTagOT:id>,
          "titleForMarker": "OT",
          "titleForEndUser": "OT",
          "slug": "ot",
          "isActive": true,
          "position": 1
      }
    ]
    """

  Scenario: Get all Story Types Order By titleForMarker ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags?order[titleForMarker]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [  
      {
          "id" : <HighLevelSubjectTagNewsJcl:id>,
          "titleForMarker": "JCL",
          "titleForEndUser": "JCL",
          "slug": "jcl",
          "isActive": true,
          "position": 3
      },
      {
          "id" : <HighLevelSubjectTagOT:id>,
          "titleForMarker": "OT",
          "titleForEndUser": "OT",
          "slug": "ot",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <HighLevelSubjectTagNewsPo:id>,
          "titleForMarker": "PO",
          "titleForEndUser": "PO",
          "slug": "po",
          "isActive": true,
          "position": 2
       }
    ]
    """

  Scenario: Get all Story Types Order By titleForMarker DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags?order[titleForMarker]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <HighLevelSubjectTagNewsPo:id>,
          "titleForMarker": "PO",
          "titleForEndUser": "PO",
          "slug": "po",
          "isActive": true,
          "position": 2
       },
      {
          "id" : <HighLevelSubjectTagOT:id>,
          "titleForMarker": "OT",
          "titleForEndUser": "OT",
          "slug": "ot",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <HighLevelSubjectTagNewsJcl:id>,
          "titleForMarker": "JCL",
          "titleForEndUser": "JCL",
          "slug": "jcl",
          "isActive": true,
          "position": 3
      }
    ]
    """

  Scenario: Delete an High Level Subject Tag
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/high_level_subject_tags/<HighLevelSubjectTagNewsPo:id>"
    Then the response status code should be 204


  Scenario: Get all Story Types Order By titleForMarker DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags?order[titleForMarker]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <HighLevelSubjectTagOT:id>,
          "titleForMarker": "OT",
          "titleForEndUser": "OT",
          "slug": "ot",
          "isActive": true,
          "position": 1
      },
      {
          "id" : <HighLevelSubjectTagNewsJcl:id>,
          "titleForMarker": "JCL",
          "titleForEndUser": "JCL",
          "slug": "jcl",
          "isActive": true,
          "position": 3
      }
    ]
    """


  Scenario: Get all Story Types Order By titleForMarker DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/high_level_subject_tags?trash=true"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <HighLevelSubjectTagNewsPo:id>,
          "titleForMarker": "PO",
          "titleForEndUser": "PO",
          "slug": "po",
          "isActive": true,
          "position": 2
       }
    ]
    """
