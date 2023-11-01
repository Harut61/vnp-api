Feature: Manage Users
  In order to manage Users
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Roles
    Given I am logged in as SuperAdmin
    And I smartly create the AdminRoles named SeeAllVideosRole with:
    """
    {
      "code": "ROLE_SEE_ALL_SOURCE_VIDEOS",
      "title": "see all source videos"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <SeeAllVideosRole:id>,
      "code": "ROLE_SEE_ALL_SOURCE_VIDEOS",
      "title": "see all source videos"
    }
    """

  Scenario: Trying to Create a Roles with existing code validate unique role
    Given I am logged in as SuperAdmin
    And I smartly create the AdminRoles named SeeAllVideosRoleE1 with:
    """
    {
      "code": "ROLE_SEE_ALL_SOURCE_VIDEOS",
      "title": "see all source videos"
    }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
    {
      "violations": [
        {
          "propertyPath": "code",
          "message": "Role Already Exist!"
        }
      ]
    }
    """

  Scenario: Create a Roles
    Given I am logged in as SuperAdmin
    And I smartly create the AdminRoles named manageStories with:
    """
    {
      "code": "ROLE_MANAGE_STORIES",
      "title": "manage stories"
    }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
      "id" : <manageStories:id>,
      "code": "ROLE_MANAGE_STORIES",
      "title": "manage stories"
    }
    """

  Scenario: Create a Roles
    Given I am logged in as SuperAdmin
    And I smartly update the AdminRoles named manageStories with:
    """
    {
      "code": "ROLE_MANAGE_STORY",
      "title": "manage story"
    }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <manageStories:id>,
      "code": "ROLE_MANAGE_STORY",
      "title": "manage story"
    }
    """
