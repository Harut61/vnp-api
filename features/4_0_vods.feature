Feature: Manage Vods
  In order to manage Vods
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  @create_Vod
  Scenario: Create a Vods
    Given I am logged in as SuperAdmin
    And I smartly create the vod named Vod1 with:
    """
     {
        "originalExtension": "ts",
        "originalFileName": "Abc World News Tonight With David Muir - 2020 06 15, 15 30 - S07",
        "playBackId": "fe01ce2a7fbac8fafaed7c982a04e229",
        "totalSize": 190,
        "videoWidth": 1080,
        "videoHeight": 720,
        "duration": "200",
        "videoCodec": "h264",
        "videofps": "30",
        "videoBitrate": "815684",
        "displayAspectRation": "string",
        "audioCodec": "10202",
        "audioBitrate": "20202",
        "videoPath": "/3/eccbc87e4b5ce2fe28308fd9f2a7baf3.mp4",
        "title": "Abc World News Tonight With David Muir - 2020 06 15, 15 30 - S07 ",
        "description": "Demo Description Abc World News Tonight With David Muir",
        "online": true,
        "status": "INITIALIZED"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
    {
        "id" : <Vod1:id>,
        "videoBitrate": "815684",
        "displayAspectRation": "string",
        "audioCodec": "10202",
        "audioBitrate": "20202",
        "videoPath": "/3/eccbc87e4b5ce2fe28308fd9f2a7baf3.mp4",
        "title": "Abc World News Tonight With David Muir - 2020 06 15, 15 30 - S07 ",
        "description": "Demo Description Abc World News Tonight With David Muir",
        "online": true,
        "status": "INITIALIZED"
    }
    """


Scenario: Create a Vods same as time uploading
    Given I am logged in as SuperAdmin
    And I smartly create the vod named Vod2 with:
    """
     {
        "originalExtension": "ts",
        "originalFileName": "Abc World News Tonight - 2020 03 21, 17 30 - S09",
        "title": "Abc World News Tonight - 2020 03 21, 17 30 - S09",
        "status": "INITIALIZED"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
    {
      "id" : <Vod2:id>,
      "originalExtension": "ts",
      "originalFileName": "Abc World News Tonight - 2020 03 21, 17 30 - S09",
      "title": "Abc World News Tonight - 2020 03 21, 17 30 - S09",
      "status": "INITIALIZED"
    }
    """
