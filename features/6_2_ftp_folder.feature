Feature: Manage Ftp Server
#  In order to manage Ftp Folder
#  As a client software developer
#  I need to be able to retrieve, create and update them through the API.
#
#  @createFtpFolder
#  Scenario: Initialize Ftp Folder
#    Given I am logged in as SuperAdmin
#    And I send a smart "Get" request to "/time_zones/1"
#    Then the response status code should be 200
#    And save the entity TimeZone named timeZone1
#    And I smartly create the FtpFolder named FtpFolder1 with:
#    """
#     {
#        "path": "/demo/show1",
#        "show": "/shows/1",
#        "source": "/sources/1",
#        "localDropIns": [
#            "/sources/2",
#            "/sources/3"
#        ],
#        "ftpServer": "/ftp_servers/1",
#        "publishedAt": "23-03-2021 01:09:34 pm",
#        "timeZone": "/time_zones/1",
#        "isActive": true
#      }
#    """
#    Then the response status code should be 500
#    And the JSON should be a superset of:
#    """
#    {
#        "violations": [
#              {
#              }
#        ]
#      }
#    """
#
#  Scenario: Update Ftp Folder
#    Given I am logged in as SuperAdmin
#    And I smartly update the FtpFolder named FtpFolder1 with:
#    """
#     {
#       "contactName": "ABC CEO JOHN",
#        "contactEmail": "admin@abcnews.com",
#        "contactPhone": "+11234596394"
#      }
#    """
#    Then the response status code should be 200
#    And the JSON should be a smart superset of:
#    """
#    {
#        "id" : <FtpFolder1:id>,
#         "contactName": "ABC CEO JOHN",
#        "contactEmail": "admin@abcnews.com",
#        "contactPhone": "+11234596394"
#    }
#    """
#
#  Scenario: Update Ftp Folder With Error
#    Given I am logged in as SuperAdmin
#    And I smartly create the FtpFolder named FtpFolderE with:
#    """
#     {
#      "contactPhone": "+11234596394"
#      }
#    """
#    Then the response status code should be 422
#    And the JSON should be a smart superset of:
#    """
#     {
#        "violations": [
#              {
#                "propertyPath": "path",
#                "message": "This value should not be null."
#              },
#              {
#                "propertyPath": "password",
#                "message": "This value should not be blank."
#              },
#              {
#                "propertyPath": "password",
#                "message": "This value should not be blank."
#              }
#        ]
#      }
#    """