/* eslint-env amd, jasmine */

define([
  'common/lodash',
  'common/moment',
  'mocks/apis/absence-period-api-mock',
  'mocks/apis/absence-type-api-mock',
  'mocks/apis/entitlement-api-mock',
  'mocks/apis/entitlement-log-api-mock',
  'mocks/apis/option-group-api-mock',
  'leave-absences/absence-tab/components/annual-entitlement-change-log.component'
], function (_, moment) {
  describe('Annual entitlement change log', function () {
    var $provide, $q, $rootScope, AbsencePeriod, AbsenceType, ctrl, Entitlement;
    var contactId = 204;
    var periodId = 304;

    beforeEach(module('leave-absences.mocks', 'absence-tab',
    function (_$provide_) {
      $provide = _$provide_;
    }));

    beforeEach(inject(function (AbsencePeriodAPIMock, AbsenceTypeAPIMock,
    EntitlementAPIMock, EntitlementLogAPIMock, OptionGroupAPIMock) {
      $provide.value('AbsencePeriodAPI', AbsencePeriodAPIMock);
      $provide.value('AbsenceTypeAPI', AbsenceTypeAPIMock);
      $provide.value('EntitlementAPI', EntitlementAPIMock);
      $provide.value('EntitlementLogAPI', EntitlementLogAPIMock);
      $provide.value('api.optionGroup', OptionGroupAPIMock);
    }));

    beforeEach(inject(function ($componentController, _$q_, _$rootScope_,
    _AbsencePeriod_, _AbsenceType_, _Entitlement_) {
      $q = _$q_;
      $rootScope = _$rootScope_;
      AbsencePeriod = _AbsencePeriod_;
      AbsenceType = _AbsenceType_;
      Entitlement = _Entitlement_;

      spyOn(AbsencePeriod, 'all').and.callThrough();
      spyOn(Entitlement, 'logs').and.callThrough();

      ctrl = $componentController('annualEntitlementChangeLog', null, {
        contactId: contactId,
        periodId: periodId
      });
    }));

    describe('on init', function () {
      it('sets absence period equal to null', function () {
        expect(ctrl.absencePeriod).toBe(null);
      });

      it('sets absence types equal to an empty array', function () {
        expect(ctrl.absenceTypes).toEqual([]);
      });

      it('sets change log rows equal to an empty array', function () {
        expect(ctrl.changeLogRows).toEqual([]);
      });

      it('sets loading component to true', function () {
        expect(ctrl.loading.component).toBe(true);
      });

      it('gets the absence period using the provided period id', function () {
        expect(AbsencePeriod.all).toHaveBeenCalledWith({
          id: periodId
        });
      });
    });

    describe('after init', function () {
      var expectedAbsenceTypes, expectedAbsencePeriod;

      beforeEach(function () {
        AbsencePeriod.all({
          id: periodId
        })
        .then(function (periods) {
          expectedAbsencePeriod = periods[0];
        });

        AbsenceType.all().then(function (absenceTypes) {
          expectedAbsenceTypes = absenceTypes.map(function (absenceType) {
            return _.extend({
              'calculation_unit.name': jasmine.any(String),
              'calculation_unit.label': jasmine.any(String)
            }, absenceType);
          });
        });

        $rootScope.$digest();
      });

      it('got change logs for the given contact and period', function () {
        expect(Entitlement.logs).toHaveBeenCalledWith({
          contact_id: contactId,
          period_id: periodId
        });
      });

      it('stores the absence period', function () {
        expect(ctrl.absencePeriod).toEqual(expectedAbsencePeriod);
      });

      it('stores absence types', function () {
        expect(ctrl.absenceTypes).toEqual(expectedAbsenceTypes);
      });

      it('sets loading component to false', function () {
        expect(ctrl.loading.component).toBe(false);
      });

      describe('entitlement log rows', function () {
        var allEntitlements, entitlementLogRows,
          expectedEntitlementLogRowsStructure;

        beforeEach(function () {
          var filters = { contact_id: contactId, period_id: periodId };

          $q.all([
            Entitlement.all(filters),
            Entitlement.logs(filters)
          ])
          .then(function (entitlementsAndLogs) {
            allEntitlements = entitlementsAndLogs[0];
            entitlementLogRows = _.chain(entitlementsAndLogs).flatten()
              .groupBy(groupByCreationDateRoundedSeconds).toArray().value();
          })
          .then(function () {
            var indexedEntitlements = _.indexBy(allEntitlements, 'type_id');

            var entitlements = ctrl.absenceTypes.map(function (absenceType) {
              var hasEntitlementForAbsenceType = !!indexedEntitlements[absenceType.id];

              /**
               * Skip if there are no entitlements related to the absence type
               * for this row.
               */
              if (!hasEntitlementForAbsenceType) {
                return jasmine.anything();
              }

              return jasmine.objectContaining({
                'calculation_unit': jasmine.anything(),
                'created_date': jasmine.anything(),
                'editor_id': jasmine.anything(),
                'entitlement_amount': jasmine.anything(),
                'entitlement_id': jasmine.anything(),
                'entitlement_id.type_id': jasmine.anything()
              });
            });

            expectedEntitlementLogRowsStructure = _.map(entitlementLogRows,
              function () {
                return {
                  date: jasmine.anything(),
                  entitlements: entitlements
                };
              });
          });

          $rootScope.$digest();
        });

        it('stores one row for each entitlement logs and current entitlements grouped by their creation date', function () {
          expect(ctrl.changeLogRows).toEqual(expectedEntitlementLogRowsStructure);
        });

        describe('each row', function () {
          it('contains the creation date as a moment object', function () {
            expect(moment.isMoment(ctrl.changeLogRows[0].date)).toBe(true);
          });

          it('contains a list of leave entitlements', function () {
            expect(ctrl.changeLogRows[0].entitlements.length)
              .toBe(ctrl.absenceTypes.length);
          });

          describe('entitlements order', function () {
            var absenceTypeIds, entitlementIds;

            beforeEach(function () {
              absenceTypeIds = _.pluck(ctrl.absenceTypes, 'id');
              entitlementIds = _.pluck(ctrl.changeLogRows[0].entitlements,
                'entitlement_id.type_id');
            });

            it('stores the entitlements in the same order as the absence used for the header', function () {
              expect(entitlementIds).toEqual(absenceTypeIds);
            });
          });

          describe('entitlements calculation units', function () {
            var absenceTypeCalculationUnits, entitlementCalculationUnits;

            beforeEach(function () {
              absenceTypeCalculationUnits = _.pluck(ctrl.absenceTypes,
                'calculation_unit.name');
              entitlementCalculationUnits = _.pluck(ctrl.changeLogRows[0]
                .entitlements, 'calculation_unit');
            });

            it('stores the calculation unit name for the entitlement', function () {
              expect(entitlementCalculationUnits).toEqual(absenceTypeCalculationUnits);
            });
          });
        });

        describe('change log rows order', function () {
          var originalRowsOrder, expectedRowsOrder;

          beforeEach(function () {
            originalRowsOrder = _.pluck(ctrl.changeLogRows, 'date')
              .map(function (momentDate) {
                return momentDate.toDate();
              });
            expectedRowsOrder = _.clone(originalRowsOrder).sort(function (a, b) {
              return moment(b).diff(a);
            });
          });

          it('stores the change log rows in descending order of their creation date', function () {
            expect(originalRowsOrder).toEqual(expectedRowsOrder);
          });
        });
      });
    });

    /**
     * Helper function that groups entitlements by their creation date. The
     * creation date is rounded to the nearest 15 second interval (0, 15, 30,
     * 45, and 0 + 1 minute) in order to group entitlements that were created
     * at the same time, but have a difference of a few seconds between them.
     *
     * @param {Object} entitlement - the entitlement change log to grab the
     * creation date from.
     * @return {String} - ISO date string of the rounded creation date.
     */
    function groupByCreationDateRoundedSeconds (entitlement) {
      var date = moment(entitlement.created_date);
      var secondIntervals = 15;
      var roundedSeconds = Math.round(date.seconds() / secondIntervals) *
        secondIntervals;

      date.seconds(roundedSeconds);

      return date.toISOString();
    }
  });
});
