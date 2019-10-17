## Release 3.3.1

* Major performance improvement to "Add to Batch".

## Release 3.3
**In this release we update profiles to use the declaration eligibility field instead of the contribution.
This allows us to create a new declaration (as it will be the user filling in a profile via contribution page etc.)
 and means we don't create a declaration when time a contribution is created / imported with the "eligible" flag set to Yes.**

**IMPORTANT: Make sure you run the extension upgrades (3104).**

* Fix status message on AddToBatch.
* Fix crash on enable/disable extension.
* Fix creating declarations every time we update a contribution.
* Refactor insert/updateDeclaration.
* Refactor loading of optiongroups/values - we load them in the upgrader in PHP meaning that we always ensure they are up to date with the latest extension.
* Add documentation in mkdocs format (just extracted from README for now).
* Make sure we properly handle creating/ending and creating a declaration again (via eg. contribution page).
* Allow for both declaration eligibility and individual contribution eligibility to be different on same profile (add both fields).
* Fix PHP notice in GiftAid report.
* Match on OptionValue value when running upgrader as name is not always consistent.

## Release 3.2
* Be stricter checking eligible_for_gift_aid variable type 
* Fix issues with entity definition and regenerate
* Fix PHP notice
* Refactor addtobatch for performance, refactor upgrader for reliability
* Add API to update the eligible_for_gift_aid flag on contributions

## Release 3.1
* Be stricter checking eligible_for_gift_aid variable type


