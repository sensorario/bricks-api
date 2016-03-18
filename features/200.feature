Feature: all end points must be available

    Scenario Outline: a client look for an end point
        Given go to <uri>
        Then the response status code should be 200

    Examples:
        | uri         |
        | "/homepage" |
        | "/stats"    |
        | "/shops"    |
        | "/insights" |
