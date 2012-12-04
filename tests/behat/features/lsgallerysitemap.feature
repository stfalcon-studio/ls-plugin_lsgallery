Feature: Lsgallery plugin standart features BDD
  Test link of lsgallery and sitemap functionality of LiveStreet lsgallery plugin
    Scenario: Check is album in sitemap
      Given I load fixtures for plugin "lsgallery"
      Then check is plugin active "sitemap"
      Given I am on "/sitemap_albums_1.xml"
      Then content type is "application/xml"
      Then the response status code should be 200
      Then the response should contain "/gallery/album/1"