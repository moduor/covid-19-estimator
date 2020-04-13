<?php

    /*
        {
            region: {
                name: "Africa",
                avgAge: 19.7,
                avgDailyIncomeInUSD: 5,
                avgDailyIncomePopulation: 0.71
            },
            periodType: "days",
            timeToElapse: 58,
            reportedCases: 674,
            population: 66622705,
            totalHospitalBeds: 1380614
        }
    */

    function covid19ImpactEstimator($data)
    {
        $impact = array();
        $severeImpact = array();

        $receivedData = json_decode($data, true);
        $reportedCases = $receivedData['reportedCases'];

        $impCurrentlyInfected = $reportedCases * 10; // Impact currently inected cases
        $severeImpCurrentlyInfected = $reportedCases * 50; // Severe Impact currently inected cases
        $impInfByReqtime = $impCurrentlyInfected * 512; // Impact - infectionsByRequestedTime = $severeImpCurrentlyInfected *(2^9)
        $severeImpInfByReqtime = $severeImpCurrentlyInfected * 512; // Severe Impact - infectionsByRequestedTime = $severeImpCurrentlyInfected * 512
        $impSevCasesByReqTime = (int)($impInfByReqtime * 0.15); //Impact  - severeCasesByRequestedTime
        $servreImpSevCasesByReqTime = (int)($severeImpInfByReqtime * 0.15); //Sever Impact severeCasesByRequestedTime


        $impact['currentlyInfected'] = $impCurrentlyInfected;
        $severeImpact['currentlyInfected'] = $severeImpCurrentlyInfected;
        $impact['infectionsByRequestedTime'] = $impInfByReqtime;
        $severeImpact['infectionsByRequestedTime'] = $severeImpInfByReqtime;

        // Challenge 2
        $impact['severeCasesByRequestedTime'] = $impSevCasesByReqTime;
        $severeImpact['severeCasesByRequestedTime'] = $servreImpSevCasesByReqTime;
        $availableBeds =  (int)($receivedData['totalHospitalBeds'] * .35);

        $exportData = array(
            "data" => $receivedData, // the input data you got
            "impact" => $impact, // your best case estimation
            "severeImpact" => $severeImpact, // your severe case estimation
        );

        $exportData = json_encode($exportData);

        return $exportData;
    }