Feature: Lsgallery plugin standart features BDD
  Test base functionality by the main user logined

  Scenario: Check album as guest Friend
    Then check is plugin active "lsgallery"
    Given I load fixtures for plugin "lsgallery"
    Given I am on "/login"
    Then I want to login as "user-golfer"

  #Check albums in public list
    Then I am on "/gallery/albums"

    Then I should see in element by css "content" values:
      | value |
      | /gallery/album/1">album opened</a> |

    Then I should not see in element by css "content" values:
      | value |
      | /gallery/album/2">album personal</a> |
      | /gallery/album/3">album friend</a> |

  #check images in sidebar
    Then I should see in element by css "sidebar" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |

  #Check user's albums on userpage
    Then I am on "/profile/user-golfer/"
    Then I should see in element by css "content" values:
      | value |
      | /gallery/album/1"> |
      | /gallery/album/2"> |
      | /gallery/album/3"> |

  #Check user's created albums
    Then I am on "/profile/user-golfer/created/albums/"

    Then I should see in element by css "content" values:
      | value |
      | /gallery/album/1">album opened</a> |
      | /gallery/album/2">album personal</a> |
      | /gallery/album/3">album friend</a> |

  #Check for protected albums permissions
    Then I am on "/gallery/album/2"
    Then I should see in element by css "content" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test4 |
      | http://livestreet.test/uploads/images/lsgallery/test5 |

    Then I should see in element by css "block_album" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test4 |
      | test album personal description text |

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

    Then I should see in element by css "stream-images" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |

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

  #Check for image from private album
    Then I am on "/gallery/image/4"
    Then I should see in element by css "content" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test4 |
      | http://livestreet.test/uploads/images/lsgallery/test5 |


  #Check images on 'new' page
    Then I am on "/gallery/photo/new/"

    Then I should see in element by css "content" values:
      | value |
      | /gallery/image/1"> |
      | /gallery/image/2"> |

    Then I should not see in element by css "content" values:
      | value |
      | /gallery/image/3"> |

    Then I should see in element by css "stream-images" values:
      | value |
      | http://livestreet.test/uploads/images/lsgallery/test2 |
      | http://livestreet.test/uploads/images/lsgallery/test3 |
