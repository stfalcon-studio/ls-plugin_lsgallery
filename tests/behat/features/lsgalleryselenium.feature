Feature: Lsgallery plugin standart features BDD
  Test base functionality of LiveStreet lsgallery plugin standart

    Scenario: Check for activity of plugin
      Then check is plugin active "lsgallery"

    @mink:selenium2
    Scenario: Set Like to image
      Given I load fixtures for plugin "lsgallery"

      Given I am on "/login/"
      Then I fill the element "#login" value "user-golfer"
      And I fill the element "#password" value "qwerty"
      Then I press button css "#login-form-submit"
      Then I wait "1000"
      Given I am on "/gallery/image/3"
      Then I wait "1000"
      Then I should see in element "content" values:
        | value  |
        | <i id="fav_image_3" class="favourite "></i> |
        | <span class="favourite-count" id="fav_count_image_3"></span> |

      Then I press button css ".topic-info .topic-info-favourite"
      Then I wait "1000"
      Then I should see in element "content" values:
        | value  |
        | <i id="fav_image_3" class="favourite active"></i> |
        | <span class="favourite-count" id="fav_count_image_3">1</span> |

      Given I am on "/profile/user-golfer/favourites/images/"
      Then I should see in element "content" values:
        | value  |
        | gallery/image/3"><img |