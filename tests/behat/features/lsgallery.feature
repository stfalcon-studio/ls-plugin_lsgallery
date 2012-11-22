Feature: Lsgallery plugin standart features BDD
  Test base functionality of LiveStreet lsgallery plugin standart

    Scenario: Load fixtures
      Given I am activated plugin "lsgallery"
      Given I load fixtures for plugin "lsgallery"

    Scenario: Check for albums
      Then I want to login as "admin"
      Given I am on "/profile/admin/created/albums/"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /gallery/album/1">album opened</a> |
        | /gallery/album/2">album personal</a> |
        | /gallery/album/3">album friend</a> |

      Given I am on "/gallery/albums/"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /gallery/album/1">album opened</a> |
        | /uploads/images/lsgallery/test2 |
      Then I should not see "/gallery/album/2\">album personal</a>"

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
      Given I am on "/gallery/image/2"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /uploads/images/lsgallery/test3 |
        | /uploads/images/lsgallery/test2 |

    Scenario: View is image in personal album
      Then I want to login as "admin"

      Given I am on "/gallery/image/3"
      Then the response status code should be 200
      Then the response have sets:
        | value  |
        | /uploads/images/lsgallery/test4 |
        | /uploads/images/lsgallery/test5 |

    Scenario: Check is album in sitemap
    # if sitemap is not active, test will fail and next tests are skipping
      Then check is plugin active "sitemap"

      Given I am on "/sitemap_albums_1.xml"
      Then content type is "application/xml"
      Then the response status code should be 200
      Then the response should contain "/gallery/album/1"
