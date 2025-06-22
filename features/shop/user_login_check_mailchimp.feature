@shop @mailchimp
Feature: Check Mailchimp subscription on shop login

  Background:
    Given the store operates on a channel named "Mailchimp Channel"
    And Mailchimp is enabled for the channel "Mailchimp Channel"
    And there is a customer account "john@example.com" identified by "password123"

  Scenario: Mailchimp subscription is checked when the user logs in
    When I sign in with email "john@example.com" and password "password123"
    Then Mailchimp should have checked if the email "john@example.com" is subscribed
