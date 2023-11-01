Feature: Manage Source Video
  In order to manage Source Video
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  @createç
  Scenario: Initialize Source Video
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users/1"
    Then the response status code should be 200
    And save the entity AdminUser named superAdminUser
    And I smartly create the SourceVideo named SourceVideo1 with:
    """
     {

        "status": "UPLOADED",
        "vod": "/vods/<Vod2:id>"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
    {
        "id" : <SourceVideo1:id>,
        "vod":  {
            "id": "<Vod2:id>"
        }
    }
    """

  Scenario: Update Source Video
    Given I am logged in as SuperAdmin
    And I smartly update the SourceVideo named SourceVideo1 with:
    """
     {
        "title": "Abc World News Tonight ",
        "show": "/shows/<ShowCBSThisMorning:id>",
        "status": "PROCESSING",
        "subtitles": [
          "/sub_titles/<Hindi:id>",
          "/sub_titles/<French:id>"
        ]
      }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    {
        "id" : <SourceVideo1:id>,
        "title": "Abc World News Tonight ",
        "show": {
            "id": <ShowCBSThisMorning:id>
        },
        "status": "PROCESSING",
        "subtitles": [
          {
            "id": <French:id>
          },
          {
            "id": <Hindi:id>
          }
        ],
        "vod":  {
            "id": <Vod2:id>
        }
    }
    """



