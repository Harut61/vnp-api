Feature: Manage Transcoding Profile
  In order to manage Transcoding Profile
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  @createç
  Scenario: Initialize Transcoding Profile
    Given I am logged in as SuperAdmin
    And I smartly create the TranscodingProfile named TranscodingProfile1 with:
    """
     {
        "title": "Test Profile",
        "isDefault": true,
        "profiles": ["/transcoding_profile_options/<TranscodingProfileOption1:id>"]
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
    {
        "id" : <TranscodingProfile1:id>,
        "title": "Test Profile",
         "isDefault": true,
        "profiles":  [{
            "id": "<TranscodingProfileOption1:id>"
        }]
    }
    """

  Scenario: Update Transcoding Profile
    Given I am logged in as SuperAdmin
    And I smartly update the TranscodingProfile named TranscodingProfile1 with:
    """
     {
       "title": "Test Profile",
       "isDefault": false
      }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    {
        "id" : <TranscodingProfile1:id>,
         "title": "Test Profile",
       "isDefault": false,
        "profiles":  [
            {
                "id": "<TranscodingProfileOption1:id>"
            }
        ]
    }
    """



