# UPGRADE FROM 1.0.0 to 1.1.0

### 1. Interface Method Renamed

The method `syncSubstriptionStateFromMailChimp()` in `CustomerListenerInterface` was renamed to `syncSubscriptionStateFromMailChimp()` to correct a typo.

If you implement this interface, you must update your implementation accordingly.
