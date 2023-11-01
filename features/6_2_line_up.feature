#Feature: Manage LineUp
#  In order to manage LineUp
#  As a client software developer
#  I need to be able to retrieve, create and update them through the API.
#
#  Scenario: Create a LineUp
#    Given I am logged in as SuperAdmin
#    And I smartly create the LineUp named lineUp1 with:
#    """
#    {
#
#      "vneLineupId": "334534",
#      "lineupDuration": "37.56",
#      "firstLineUp":true,
#      "longitude":45.677,
#      "latitude":67.899
#
#    }
#    """
#    Then the response status code should be 201
#    And the JSON should be a smart superset of:
#    """
#      {
#      "id" : <lineUp1:id>,
#      "vneLineupId": "334534",
#      "lineupDuration": "37.56",
#      "firstLineUp":true,
#      "longitude":45.677,
#      "latitude":67.899
#    }
#    """
#
#  Scenario: Create a LineUp
#    Given I am logged in as SuperAdmin
#    And I smartly create the LineUp named lineUp2 with:
#    """
#    {
#
#      "vneLineupId": "334",
#      "lineupDuration": "356",
#      "firstLineUp":true,
#      "longitude":45.677,
#      "latitude":67.899
#    }
#    """
#    Then the response status code should be 201
#    And the JSON should be a smart superset of:
#    """
#      {
#      "id" : <lineUp2:id>,
#      "vneLineupId": "334",
#      "lineupDuration": "356",
#      "firstLineUp":true,
#      "longitude":45.677,
#      "latitude":67.899
#    }
#    """
#
#  Scenario: Create a LineUp
#    Given I am logged in as SuperAdmin
#    And I smartly create the LineUp named lineUp3 with:
#    """
#    {
#
#      "vneLineupId": "33213134",
#      "lineupDuration": "35.236"
#    }
#    """
#    Then the response status code should be 201
#    And the JSON should be a smart superset of:
#    """
#      {
#      "id" : <lineUp3:id>,
#      "vneLineupId": "33213134",
#      "lineupDuration": "35.236"
#    }
#    """
#
#  Scenario: Get List of LineUp
#    Given I am logged in as SuperAdmin
#    And I send a smart "Get" request to "/line_ups"
#    Then the response status code should be 200
#    And the JSON should be a smart superset of:
#  """
#  [
#    {
#      "id" : <lineUp3:id>,
#      "vneLineupId": "33213134",
#      "lineupDuration": "35.236"
#    },
#    {
#      "id" : <lineUp2:id>,
#      "vneLineupId": "334",
#      "lineupDuration": "356"
#    },
#    {
#      "id" : <lineUp1:id>,
#      "vneLineupId": "334534",
#      "lineupDuration": "37.56"
#    }
#  ]
#  """
#
#  Scenario: Delete an LineUp
#    Given I am logged in as SuperAdmin
#    And I send a smart "DELETE" request to "/line_ups/<lineUp1:id>"
#    Then the response status code should be 204
#
#
#
#
#
