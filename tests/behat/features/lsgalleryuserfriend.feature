Feature: Lsgallery plugin standart features BDD
  Test base functionality of LiveStreet by the friend user logined

@mink:selenium2
  Scenario: Check album as guest Friend
    Then check is plugin active "lsgallery"
    Given I load fixtures for plugin "lsgallery"
    Given I am on "/login"
    Then I want to login as "user-friend"

  #Check albums in public list
    Then I am on "/gallery/albums"

    Then I should see in element by css "content" values:
      | value |
      | /gallery/album/1">album opened</a> |
      | /gallery/album/3">album friend</a> |

    Then I should not see in element by css "content" values:
      | value |
      | /gallery/album/2">album personal</a> |

  #check images in sidebar
  Given I press element by css "#block_gallery_item_new a"
  And I wait "500"
    Then I should see in element by css "sidebar" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |
      | http://livestreet.test/uploads/images/lsgallery/test6 |
      | http://livestreet.test/uploads/images/lsgallery/test8 |
      | http://livestreet.test/uploads/images/lsgallery/test9 |

  #Check user's albums on userpage
    Then I am on "/profile/user-golfer/"
    Then I should see in element by css "content" values:
      | value |
      | /gallery/album/1"> |
      | /gallery/album/3"> |

    Then I should not see in element by css "content" values:
      | value |
      | /gallery/album/2"> |


  #Check user's created albums
    Then I am on "/profile/user-golfer/created/albums/"

    Then I should see in element by css "content" values:
      | value |
      | /gallery/album/1">album opened</a> |
      | /gallery/album/3">album friend</a> |

    Then I should not see in element by css "content" values:
      | value |
      | gallery/album/2">album personal</a> |

  #Check for protected albums permissions
    Then I am on "/gallery/album/2"
    Then I should see in element by css "content" values:
      | value |
      | <p>No access</p> |

  #Check for friend albums permissions
    Then I am on "/gallery/album/3"
    Then I should see in element by css "content" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test6 |
      | http://livestreet.test/uploads/images/lsgallery/test8 |
      | http://livestreet.test/uploads/images/lsgallery/test9 |

    Then I should see in element by css "block_album" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test6 |
      | test album friend description text |


  #Check for image from public album
    Then I am on "/gallery/image/1"
    Then I should see in element by css "content" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |

    Given I press element by css "#block_gallery_item_new a"
    And I wait "500"
    And I should see in element by css "stream-images" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |
      | http://livestreet.test/uploads/images/lsgallery/test6 |
      | http://livestreet.test/uploads/images/lsgallery/test8 |
      | http://livestreet.test/uploads/images/lsgallery/test9 |

    Then I should see in element by css "block_album" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | test album opened description text |


  #Check for image from friend album
    Then I am on "/gallery/image/6"
    Then I should see in element by css "content" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test6 |
      | http://livestreet.test/uploads/images/lsgallery/test9 |


  #Check images on 'new' page
    Then I am on "/gallery/photo/new/"

    Then I should see in element by css "content" values:
      | value |
      | /gallery/image/1"> |
      | /gallery/image/2"> |
      | /gallery/image/5"> |
      | /gallery/image/6"> |
      | /gallery/image/7"> |

    Then I should not see in element by css "content" values:
      | value |
      | /gallery/image/3"> |

    Given I press element by css "#block_gallery_item_new a"
    And I wait "500"
    Then I should see in element by css "stream-images" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |
      | http://livestreet.test/uploads/images/lsgallery/test6 |
      | http://livestreet.test/uploads/images/lsgallery/test8 |
      | http://livestreet.test/uploads/images/lsgallery/test9 |