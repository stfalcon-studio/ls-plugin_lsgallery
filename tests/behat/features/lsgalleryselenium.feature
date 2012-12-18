Feature: Lsgallery plugin standart features BDD
  Test base functionality of LiveStreet lsgallery plugin standart

    Scenario: Check for activity of plugin
      Then check is plugin active "lsgallery"

  @mink:selenium2
    Scenario: Check voite with negative user rating
      Given I load fixtures for plugin "lsgallery"

      Then I set carma "-10" to user "admin"

      Given I am on "/login"
      Then I want to login as "admin"

      Given I am on "/gallery/image/2"
      Then I wait "500"
      Then I should see in element by css "content" values:
      | value |
      | <div class="vote-item vote-down" onclick="return ls.vote.vote(2,this,-1,'image');"><span><i></i></span></div> |
      | <i onclick="return ls.vote.vote(2,this,0,'image');"></i> |
      | <div class="vote-item vote-up" onclick="return ls.vote.vote(2,this,1,'image');"><span><i></i></span></div> |
      Then I press element by css ".vote-item.vote-up"
      Then I wait "500"

      Then I should see in element by css "content" values:
      | value |
      | <div class="vote-item vote-down" onclick="return ls.vote.vote(2,this,-1,'image');"><span><i></i></span></div> |
      | <i onclick="return ls.vote.vote(2,this,0,'image');"></i> |
      | <div class="vote-item vote-up" onclick="return ls.vote.vote(2,this,1,'image');"><span><i></i></span></div> |

  @mink:selenium2
    Scenario: Check voite with positive user rating
      Given I load fixtures for plugin "lsgallery"

      Then I set carma "10" to user "admin"
      Given I am on homepage
      Then I want to login as "admin"

      Then I wait "1000"
      Given I am on "/gallery/image/2"
      Then I wait "500"
      Then I should see in element by css "vote_total_image_2" values:
        | value |
        | <i onclick="return ls.vote.vote(2,this,0,'image');"></i> |

      Then I press element by css ".vote-item.vote-up"
      Then I wait "500"

      Then I should see in element by css "vote_total_image_2" values:
        | value |
        | +1 |

  @mink:selenium2
    Scenario: Set Like to image
      Given I load fixtures for plugin "lsgallery"

      Given I am on homepage
      Then I want to login as "admin"

      Given I am on "/gallery/image/3"
      Then I wait "1000"
      Then I should see in element by css "content" values:
        | value  |
        | <i id="fav_image_3" class="favourite "></i> |
        | <span class="favourite-count" id="fav_count_image_3"></span> |

      Then I press element by css ".topic-info .topic-info-favourite"
      Then I wait "1000"
      Then I should see in element by css "content" values:
        | value  |
        | <i id="fav_image_3" class="favourite active"></i> |
        | <span class="favourite-count" id="fav_count_image_3">1</span> |

      Given I am on "/profile/admin/favourites/images/"
      Then I should see in element by css "content" values:
        | value  |
        | gallery/image/3"><img |