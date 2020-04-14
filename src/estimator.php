<?php
    function get_num_days($periodType, $timeToElapse){
        $days = 0;
        switch($periodType){
            case 'days':
                $days = $timeToElapse;
                break;
            case 'weeks':
                $days = (int)($timeToElapse * 7);
                break;
            case 'months':
                $days = (int)($timeToElapse * 30);
                break;
            default:
                $days = $timeToElapse;
        }
        return $days;
    }

    function covid19ImpactEstimator($data)
    {
        $impact = array();
        $severeImpact = array();
	$data = json_encode($data);
        $receivedData = json_decode($data, true);

        $averageInconme = $receivedData['region']['avgDailyIncomeInUSD']; // Average daily income
        $averageIncomePop = $receivedData['region']['avgDailyIncomePopulation']; // Average daily income population
        $reportedCases = $receivedData['reportedCases'];
        $periodType = $receivedData['periodType'];
        $timeToElapse = $receivedData['timeToElapse'];
        $population = $receivedData['population'];
        $totalHospitalBeds = $receivedData['totalHospitalBeds'];
        //Calculations
		$days = get_num_days($periodType, $timeToElapse);
		$factor = (int)($days/3);

        $impCurrentlyInfected = $reportedCases * 10; // Impact currently inected cases
        $severeImpCurrentlyInfected = $reportedCases * 50; // Severe Impact currently inected cases

        $impInfByReqtime = ($impCurrentlyInfected * pow(2,$factor)); // Impact - infectionsByRequestedTime = $severeImpCurrentlyInfected *(2^9)
        $severeImpInfByReqtime = ($severeImpCurrentlyInfected * pow(2,$factor)); // Severe Impact - infectionsByRequestedTime = $severeImpCurrentlyInfected * 512
        $impSevCasesByReqTime = ($impInfByReqtime * 0.15); //Impact  - severeCasesByRequestedTime
        $servreImpSevCasesByReqTime = ($severeImpInfByReqtime * 0.15); //Sever Impact - severeCasesByRequestedTime


        $impact['currentlyInfected'] = (int)$impCurrentlyInfected;
        $severeImpact['currentlyInfected'] = (int)$severeImpCurrentlyInfected;
        $impact['infectionsByRequestedTime'] = (int)$impInfByReqtime;
        $severeImpact['infectionsByRequestedTime'] = (int)$severeImpInfByReqtime;

        // Challenge 2
        $impact['severeCasesByRequestedTime'] = (int)$impSevCasesByReqTime;
        $severeImpact['severeCasesByRequestedTime'] = (int)$servreImpSevCasesByReqTime;
        $availableBeds =  ($receivedData['totalHospitalBeds'] * 0.35);

        $impact['hospitalBedsByRequestedTime'] = (int)($availableBeds - $impSevCasesByReqTime);
        $severeImpact['hospitalBedsByRequestedTime'] = (int)($availableBeds - $servreImpSevCasesByReqTime);

        /// ***** Challenge 3 ****///
        $impact['casesForICUByRequestedTime'] = (int)($impInfByReqtime * 0.05); // Impact  - Require ICU Care
        $severeImpact['casesForICUByRequestedTime'] = (int)($severeImpInfByReqtime * 0.05); // Sever Impact - Require ICU Care
        $impact['casesForVentilatorsByRequestedTime'] = (int)($impInfByReqtime * 0.02); // Impact  - Require ventilators
        $severeImpact['casesForVentilatorsByRequestedTime'] = (int)($severeImpInfByReqtime * 0.02); // Sever Impact - Require ventilators
        // Money lost
        $impactMoneyLost = (int)(($impInfByReqtime * $averageIncomePop * $averageInconme)/$days);
        $severeImpactMoneyLost = (int)(($severeImpInfByReqtime * $averageIncomePop * $averageInconme)/$days);
        $impact['dollarsInFlight'] = $impactMoneyLost; // Impact  - Dollars in flight (Money lost)
        $severeImpact['dollarsInFlight'] = $severeImpactMoneyLost; // Sever Impact - Dollars in flight (Money lost)

        $exportData = array(
            "data" => $receivedData, // the input data you got
            "impact" => $impact, // your best case estimation
            "severeImpact" => $severeImpact, // your severe case estimation
        );

        //$exportData = json_encode($exportData);

        return $exportData;
    }
