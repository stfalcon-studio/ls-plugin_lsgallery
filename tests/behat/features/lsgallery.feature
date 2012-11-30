Feature: Lsgallery plugin standart features BDD
  Test base functionality of LiveStreet lsgallery plugin standart

    Scenario: Check for activity of plugin
      Then check is plugin active "lsgallery"

    Scenario: Album Create
      Given I load fixtures for plugin "lsgallery"
      Then I want to login as "user-golfer"
      Given I am on "/gallery/create/"

      When I fill in "album_title" with "created_by_tests"
      And I fill in "album_description" with "created_description_by_tests"
      And I press button "submit_create_album"

      Then I should see "Album management: created_by_tests"

    Scenario: Check for public albums
      Given I load fixtures for plugin "lsgallery"
      Given I am on "/gallery/albums/"
      Then the response status code should be 200

      Then I should see in element "content" values:
        | value  |
        | /gallery/album/1">album opened</a> |
        | /uploads/images/lsgallery/test2 |
      Then I should not see "/gallery/album/2\">album personal</a>"

      Then I should see in element "block_gallery" values:
        | value |
        | gallery/image/2 |
        | lsgallery/test2 |
        | profile/user-golfer/">user-golfer</a> |
        | gallery/album/1">album opened</a> |

    Scenario: Check for created albums in profile
      Given I load fixtures for plugin "lsgallery"
      Then I want to login as "user-golfer"
      Given I am on "/profile/user-golfer/created/albums/"
      Then the response status code should be 200
      Then the response have sets:
        | value |
        | /gallery/album/1">album opened</a> |
        | /gallery/album/2">album personal</a> |
        | /gallery/album/3">album friend</a> |

    Scenario: Check for user albums
      Given I load fixtures for plugin "lsgallery"
      Then I want to login as "user-golfer"
      Given I am on "/profile/user-golfer"
      Then the response status code should be 200
      Then the response have sets:
        | value |
        | /gallery/album/1"> |
        | /gallery/album/2"> |
        | /gallery/album/3"> |

    Scenario: Check for album #1 (public)
      Given I load fixtures for plugin "lsgallery"
      Given I am on "/gallery/album/1"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /gallery/image/1"> |
        | /uploads/images/lsgallery/test2 |
        | /gallery/image/2"> |
        | /uploads/images/lsgallery/test3 |
        | test album opened description text |

    Scenario: View is image in opened album
      Given I load fixtures for plugin "lsgallery"
      Given I am on "/gallery/image/2"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /uploads/images/lsgallery/test3 |
        | /uploads/images/lsgallery/test2 |

    Scenario: View is image in personal album
      Given I load fixtures for plugin "lsgallery"
      Then I want to login as "admin"
      Given I am on "/gallery/image/3"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /uploads/images/lsgallery/test4 |
        | /uploads/images/lsgallery/test5 |

    Scenario: Check is album in sitemap
      Given I load fixtures for plugin "lsgallery"
      Then check is plugin active "sitemap"
      Given I am on "/sitemap_albums_1.xml"
      Then content type is "application/xml"
      Then the response status code should be 200
      Then the response should contain "/gallery/album/1"
