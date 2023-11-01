Feature: Manage Users
  In order to manage Users
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  @create_Users
  Scenario: Create a Users
    Given I am logged in as SuperAdmin
    And I send a smart "Post" request to "/admin/register"
    """
    {
      "contactInfo": "https://www.google.com",
      "email": "testing@test.com"
    }
    """
    Then the response status code should be 201
    And I send a smart "Get" request to "/admin_users/3"
    And the JSON should be a smart superset of:
    """
      {
      "contactInfo": "https://www.google.com",
      "email": "testing@test.com",
      "enabled": false
    }
    """
    Then the response status code should be 200
    And save the entity AdminUser named subUser1

  Scenario: get single Users
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users/<subUser1:id>"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <subUser1:id>,
      "contactInfo": "https://www.google.com",
      "email": "testing@test.com",
      "enabled": false
    }
    """


  Scenario: confirm registration
    Given I am logged in as SuperAdmin
    And I send a smart "Post" request to "/admin/register/confirm"
    """
    {
      "token": "123456789",
      "fullName": "Nishant Patel",
      "password": "Nishant@123"
    }
    """
    Then the response status code should be 200


  Scenario: get single Users
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users/<subUser1:id>"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <subUser1:id>,
      "fullName": "Nishant Patel",
      "contactInfo": "https://www.google.com",
      "email": "testing@test.com"
    }
    """

  Scenario: block user
    Given I am logged in as SuperAdmin
    And I smartly update the AdminUser named subUser1 with:
    """
      {
      "enabled": false
    }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <subUser1:id>,
      "fullName": "Nishant Patel",
      "contactInfo": "https://www.google.com",
      "email": "testing@test.com",
      "enabled": false
    }
    """

  Scenario: Create a Marker Users
    Given I am logged in as SuperAdmin
    And I send a smart "Post" request to "/admin/register"
    """
    {
      "contactInfo": "https://www.google.com",
      "email": "marker@ivnews.com"
    }
    """
    Then the response status code should be 201
    And I send a smart "Get" request to "/admin_users/4"
    Then the response status code should be 200
    And save the entity AdminUser named markerUser

  Scenario: get single Marker Admin User
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users/<markerUser:id>"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <markerUser:id>,
      "contactInfo": "https://www.google.com",
      "email": "marker@ivnews.com",
      "enabled": false
    }
    """

  Scenario: unblock user
    Given I am logged in as SuperAdmin
    And I smartly update the AdminUser named subUser1 with:
    """
      {
      "enabled": true
    }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <subUser1:id>,
      "fullName": "Nishant Patel",
      "contactInfo": "https://www.google.com",
      "email": "testing@test.com",
      "enabled": true
    }
    """


  Scenario: Update User
    Given I am logged in as SuperAdmin
    And I smartly update the AdminUser named subUser1 with:
    """
    {
      "fullName": "jolly Patel",
      "gender": "female",
      "contactInfo": "https://www.google.com",
      "numberOfDevices": 2
    }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <subUser1:id>,
      "fullName": "jolly Patel",
      "gender": "female",
      "contactInfo": "https://www.google.com",
      "numberOfDevices": 2
    }
    """


  Scenario: Get List of Users
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
     {
      "id" : <markerUser:id>,
      "email": "marker@ivnews.com",
      "enabled": false
    },
     {
      "id" : <subUser1:id>,
      "fullName": "jolly Patel",
      "enabled": true
    }
    ]
    """

  Scenario: confirm registration
    Given I am logged in as SuperAdmin
    And I send a smart "Post" request to "/admin/register/confirm"
    """
    {
      "token": "123456789",
      "fullName": "Marker User",
      "password": "Marker@123"
    }
    """
    Then the response status code should be 200

  Scenario: get single Users
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users/<markerUser:id>"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
      {
      "id" : <markerUser:id>,
      "fullName": "Marker User",
      "contactInfo": "https://www.google.com",
      "email": "marker@ivnews.com"
    }
    """

  Scenario: Delete an Users
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/admin_users/<markerUser:id>"
    Then the response status code should be 204


  Scenario: Get List of Users
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/admin_users"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
     {
      "id" : <subUser1:id>,
      "fullName": "jolly Patel",
      "enabled": true
      }
    ]
    """

  Scenario: confirm registration
    Given I am logged in as SuperAdmin
    And I send a smart "Post" request to "/admin/reset/password"
    """
    {
      "email": "testing@test.com"
    }
    """
    Then the response status code should be 200

  Scenario: confirm registration
    Given I am logged in as SuperAdmin
    And I send a smart "Post" request to "/admin/update/password"
    """
    {
      "otp": "1234",
      "email": "testing@test.com",
      "password": "Peanut@123"
    }
    """
    Then the response status code should be 200



