Feature:
  In order to check API status
  As a Developer
  I need to be able to query an endpoint /status

  Scenario: Check that API is alive
    Given The Domain "http://api.myproject.sandbox" is available
    When I request the status url "api/status"
    Then I should see "Staying alive"