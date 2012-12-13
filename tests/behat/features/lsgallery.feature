Feature: Lsgallery plugin standart features BDD
  Test base functionality of LiveStreet lsgallery plugin standart

    Scenario: Album Create
      Then check is plugin active "lsgallery"
      Given I load fixtures for plugin "lsgallery"
      Then I want to login as "user-golfer"
      Given I am on "/gallery/create/"

      When I fill in "album_title" with "created_by_tests"
      And I fill in "album_description" with "created_description_by_tests"
      And I press button "submit_create_album"
      Then I should see "created_by_tests"
