#Feature: Manage LineUpStories
#  In order to manage LineUpStories
#  As a client software developer
#  I need to be able to retrieve, create and update them through the API.
#
#  Scenario: Create a LineUpStories
#    Given I am logged in as SuperAdmin
#    And I smartly create the LineUpStories named lineUpStory1 with:
#    """
#    {
#      "lineUp": "/line_ups/<lineUp2:id>"
#    }
#    """
#    Then the response status code should be 201
#    And the JSON should be a smart superset of:
#    """
#      {
#      "id" : <lineUpStory1:id>
#    }
#    """
#  Scenario: Create a LineUpStories
#    Given I am logged in as SuperAdmin
#    And I smartly create the LineUpStories named lineUpStory2 with:
#    """
#    {
#      "lineUp": "/line_ups/<lineUp2:id>"
#    }
#    """
#    Then the response status code should be 201
#    And the JSON should be a smart superset of:
#    """
#      {
#      "id" : <lineUpStory2:id>
#    }
#    """
#
#  Scenario: Create a LineUpStories
#    Given I am logged in as SuperAdmin
#    And I smartly create the LineUpStories named lineUpStory3 with:
#    """
#    {
#      "lineUp": "/line_ups/<lineUp3:id>"
#    }
#    """
#    Then the response status code should be 201
#    And the JSON should be a smart superset of:
#    """
#      {
#      "id" : <lineUpStory3:id>
#    }
#    """
#
#  Scenario: Get List of LineUpStories
#    Given I am logged in as SuperAdmin
#    And I send a smart "Get" request to "/line_up_stories"
#    Then the response status code should be 200
#    And the JSON should be a smart superset of:
#  """
#  [
#    {
#      "id" : <lineUpStory3:id>
#    },
#    {
#     "id" : <lineUpStory2:id>
#    },
#    {
#      "id" : <lineUpStory1:id>
#    }
#  ]
#  """
#
#  Scenario: Delete an LineUpStories
#    Given I am logged in as SuperAdmin
#    And I send a smart "DELETE" request to "/line_up_stories/<lineUpStory1:id>"
#    Then the response status code should be 204
#
#
