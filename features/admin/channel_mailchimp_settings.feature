@channel_settings @mailchimp
Feature: Configure Mailchimp for a channel
    In order to integrate Mailchimp
    As an Administrator
    I want to be able to configure Mailchimp settings per channel

    Background:
        Given the store operates on a channel named "Web Store"
        And I am logged in as an administrator

    Scenario: Admin can configure Mailchimp settings on a channel
        When I go to the "Web Store" channel edit page
        And I enable Mailchimp for the channel
        And I enable double opt-in for the channel
        And I select "eshop_en_list_id" as the Mailchimp list for the channel
        And I save the channel
        Then I should be notified that it has been successfully edited
        And the Mailchimp settings for channel "Web Store" should be:
            | isMailChimpEnabled                | true             |
            | isMailChimpListDoubleOptInEnabled | true             |
            | mailChimpListId                   | eshop_en_list_id |
