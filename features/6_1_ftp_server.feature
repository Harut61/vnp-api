Feature: Manage Ftp Server
  In order to manage Ftp Server
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  @createç
  Scenario: Initialize Ftp Server
    Given I am logged in as SuperAdmin
    And I smartly create the FtpServer named FtpServer1 with:
    """
     {
        "contactName": "ABC CEO",
        "contactEmail": "admin@abcnews.com",
        "contactPhone": "+11234596394",
        "username": "test",
        "password": "test",
        "port": "21",
        "host": "localhost",
        "protocol": "sftp",
        "isActive": true
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
    {
        "id" : <FtpServer1:id>,
        "contactName": "ABC CEO",
        "contactEmail": "admin@abcnews.com",
        "contactPhone": "+11234596394",
        "username": "test",
        "password": "test",
        "isActive": true,
        "port": "21",
        "host": "localhost",
        "protocol": "sftp"
    }
    """

  Scenario: Update Ftp Server
    Given I am logged in as SuperAdmin
    And I smartly update the FtpServer named FtpServer1 with:
    """
     {
       "contactName": "ABC CEO JOHN",
        "contactEmail": "admin@abcnews.com",
        "contactPhone": "+11234596394"
      }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    {
        "id" : <FtpServer1:id>,
         "contactName": "ABC CEO JOHN",
        "contactEmail": "admin@abcnews.com",
        "contactPhone": "+11234596394"
    }
    """

  Scenario: Update Ftp Server With Error
    Given I am logged in as SuperAdmin
    And I smartly create the FtpServer named FtpServerE with:
    """
     {
      "contactPhone": "+11234596394"
      }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
     {
        "violations": [
              {
                "propertyPath": "username",
                "message": "This value should not be blank."
              },
              {
                "propertyPath": "password",
                "message": "This value should not be blank."
              },
              {
                "propertyPath": "contactName",
                "message": "This value should not be blank."
              }
        ]
      }
    """


