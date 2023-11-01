Feature: Manage SubTitles
  In order to manage SubTitles
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a SubTitle English
    Given I am logged in as SuperAdmin
    And I smartly create the SubTitle named English with:
    """
    {
      "subLang": "English",
      "resourceUrl": "/1/english.srt"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <English:id>,
       "subLang": "English",
       "resourceUrl": "/1/english.srt"
    }
    """

  Scenario: Create a SubTitle French
    Given I am logged in as SuperAdmin
    And I smartly create the SubTitle named French with:
    """
    {
       "subLang": "French",
       "resourceUrl": "/1/french.srt"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
       "id" : <French:id>,
       "subLang": "French",
       "resourceUrl": "/1/french.srt"
    }
    """

  Scenario: Create a SubTitle Entertainment
    Given I am logged in as SuperAdmin
    And I smartly create the SubTitle named Hindi with:
    """
    {
       "subLang": "Hindi",
       "resourceUrl": "/1/hindi.srt"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <Hindi:id>,
       "subLang": "Hindi",
       "resourceUrl": "/1/hindi.srt"
    }
    """

  Scenario: Create a SubTitle NULL Title
    Given I am logged in as SuperAdmin
    And I smartly create the SubTitle named ShowNullTitle with:
    """
    {
     
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
            "propertyPath": "subLang",
            "message": "This value should not be null."
          },
          {
              "propertyPath": "subLang",
              "message": "This value should not be blank."
          },
          {
              "propertyPath": "resourceUrl",
              "message": "This value should not be null."
          },
          {
              "propertyPath": "resourceUrl",
              "message": "This value should not be blank."
          }
        ]
      }
    """

  Scenario: Create a SubTitle Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the SubTitle named ShowNullTitle with:
    """
    {
      "subLang": ""
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
              "propertyPath": "subLang",
              "message": "This value should not be blank."
          },
          {
              "propertyPath": "resourceUrl",
              "message": "This value should not be null."
          },
          {
              "propertyPath": "resourceUrl",
              "message": "This value should not be blank."
          }
        ]
      }
    """

  Scenario: Create a SubTitle Empty Title
    Given I am logged in as SuperAdmin
    And I smartly create the SubTitle named ShowNullTitle with:
    """
    {
      "subLang": "Demo",
      "resourceUrl": ""
     
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
              "propertyPath": "resourceUrl",
              "message": "This value should not be blank."
          }
        ]
      }
    """

  Scenario: Get all Story Types Order By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sub_titles"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <Hindi:id>,
          "subLang": "Hindi",
          "resourceUrl": "/1/hindi.srt"
      },
      {
          "id" : <French:id>,
          "subLang": "French",
          "resourceUrl": "/1/french.srt"
      },
       {
          "id" : <English:id>,
          "subLang": "English",
          "resourceUrl": "/1/english.srt"
       }
    ]
    """


  Scenario: Get all Story Types Order By Title ASC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sub_titles?order[subLang]"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <English:id>,
          "subLang": "English",
          "resourceUrl": "/1/english.srt"
      },
      {
          "id" : <French:id>,
          "subLang": "French",
          "resourceUrl": "/1/french.srt"
      },
      {
          "id" : <Hindi:id>,
          "subLang": "Hindi",
          "resourceUrl": "/1/hindi.srt"
      }
    ]
    """

  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sub_titles?order[subLang]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <Hindi:id>,
          "subLang": "Hindi",
          "resourceUrl": "/1/hindi.srt"
      },
      {
          "id" : <French:id>,
          "subLang": "French",
          "resourceUrl": "/1/french.srt"
      },
      {
          "id" : <English:id>,
          "subLang": "English",
          "resourceUrl": "/1/english.srt"
       }
    ]
    """

  Scenario: Delete an SubTitle
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/sub_titles/<English:id>"
    Then the response status code should be 204


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sub_titles?order[subLang]=desc"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <Hindi:id>,
          "subLang": "Hindi",
          "resourceUrl": "/1/hindi.srt"
      },
      {
          "id" : <French:id>,
          "subLang": "French",
          "resourceUrl": "/1/french.srt"
      }
    ]
    """


  Scenario: Get all Story Types Order By Title DESC
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/sub_titles?trash=true"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
          "id" : <English:id>,
          "subLang": "English",
          "resourceUrl": "/1/english.srt"
       }
    ]
    """
