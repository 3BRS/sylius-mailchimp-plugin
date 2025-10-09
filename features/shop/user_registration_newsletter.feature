@user_registration @mailchimp
Feature: Mailchimp subscription on user registration
  In order to stay updated
  As a customer
  I want to be subscribed to the newsletter when I register

  Background:
    Given the store operates on a channel named "Web Store"
    And I am logged in as an administrator
    And I go to the "Web Store" channel edit page
    And I enable Mailchimp for the channel
    And I enable double opt-in for the channel
    And I select "eshop_en_list_id" as the Mailchimp list for the channel
    And I save the channel

  Scenario: Registering with newsletter subscription should call Mailchimp API
    When I want to register a new account
    And I specify the first name as "Adham"
    And I specify the last name as "Kandeel"
    And I specify the email as "a@a.com"
    And I specify the password as "1234"
    And I confirm this password
    And I subscribe to the newsletter
    And I specify the phone number as "1234"
    And I register this account
    Then I should be notified that new account has been successfully created
    And Mailchimp API should have been called to subscribe "a@a.com"
