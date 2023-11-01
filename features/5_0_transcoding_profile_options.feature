Feature: Manage Transcoding Profile Option
  In order to manage Transcoding Profile Option
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  @createç
  Scenario: Initialize Transcoding Profile Option
    Given I am logged in as SuperAdmin
    And I smartly create the TranscodingProfileOption named TranscodingProfileOption1 with:
    """
     {
        "title" : "Test HD – 720p",
        "description" : "SHD (Standard HD)",
        "fps" : 30,
        "audioCodec" : "AAC",
        "videoWidth" : 1280,
        "videoHeight" : 720,
        "videoBitrate" : 1024000,
        "audioBitrate" : 128000,
        "profile" : "HIGH",
        "videoCodec" : "H_264"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
    {
        "id" : <TranscodingProfileOption1:id>,
        "title" : "Test HD – 720p",
        "description" : "SHD (Standard HD)",
        "fps" : 30,
        "audioCodec" : "AAC",
        "videoWidth" : 1280,
        "videoHeight" : 720,
        "videoBitrate" : 1024000,
        "audioBitrate" : 128000,
        "profile" : "HIGH",
        "videoCodec" : "H_264"
    }
    """

  Scenario: Update Transcoding Profile Option
    Given I am logged in as SuperAdmin
    And I smartly update the TranscodingProfileOption named TranscodingProfileOption1 with:
    """
     {
        "title" : "Test Updates HD – 720p",
        "description" : "Update SHD (Standard HD)",
        "fps" : 60
      }
    """
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    {
        "title" : "Test Updates HD – 720p",
        "description" : "Update SHD (Standard HD)",
        "fps" : 60
    }
    """



