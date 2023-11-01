Feature: Manage Story
  In order to manage Story
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Story Type News
    Given I am logged in as SuperAdmin
    And I smartly create the Story named StoryABCFirst with:
    """
    {
      "description": "Story Description",
      "thumbnailFrame": 0,
      "storyStart": "01:10:00",
      "ledeEndFrame": "01:21:17",
      "storyEnd": "11:21:17",
      "storyRank": 1,
      "creationStart": "2020-10-23T14:11:25.947Z",
      "creationEnd": "2020-10-23T14:11:25.947Z",
      "publishedAt": "2020-10-23T14:11:25.947Z",
      "scheduled": true,
      "ledeSubTitleText": "demo ledeSubTitleText",
      "restStorySubTitleText": "demo restStorySubTitleText",
      "title": "ABC Story"
    }
    """

## TODO ADD TEST ONCE GET CONFIRMATION ABOUT STRUCTURE