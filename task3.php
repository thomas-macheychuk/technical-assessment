<?php
const CSV_FILE_PATH = 'your_csv_file_path';
const EXPORT_FOLDER_PATH = 'your_export_folder_path';

/**
 * Imports car data from a CSV file, removes duplicates, and performs analysis.
 *
 * @param string $csvFilePath The path to the CSV file.
 * @return array An array containing a list of vehicles with valid registrations and a count of the number of vehicles with invalid registrations.
 */
function importAndAnalyzeCarData(string $csvFilePath): array
{
    $carData = [];
    $invalidRegistrations = [];
    $fuelTypes = [];

    // Open the CSV file
    if (($handle = fopen($csvFilePath, "r")) !== false) {
        // Skip the header row
        fgetcsv($handle);

        // Read each row from the CSV
        while (($row = fgetcsv($handle)) !== false) {
            // Extract data from the row
            $registration = $row[0];
            $make = $row[1];
            $model = $row[2];
            $colour = $row[3];

            // Fuel type converted to lowercase to prevent data loss for 'petrol' and 'Petrol'
            $fuel = strtolower($row[4]);

            // The fuel column in the technical test data has got three different names for 'diesel' and two for 'petrol'.
            // This should be addressed outside the code, but for this specific task they will be grouped under 'diesel' and 'petrol'.
            // In real-life environment, before making any decisions on the implementation,
            // I would flag this issue to the project owner, senior software engineer, and other stakeholders.

            $diesel = ['deisel', 'desel'];

            if (in_array($fuel, $diesel, true)) {
                $fuel = "diesel";
            } elseif ($fuel === 'petral') {
                $fuel = 'petrol';
            }

            // Check for duplicates
            // The task requires removing duplicates, therefore in a real-life situation I would ask whether the solution
            // should keep the first occurrence and discard the following duplicates, or discard duplicates with the first occurrence,
            // as duplication might mean that the data for a given car registration is invalid and should not be used.
            // For this specific task, the first occurrence is kept and the following duplicates are discarded.

            if (!isset($carData[$registration])) {
                $carData[$registration] = [
                    'car_registration' => $registration,
                    'make' => $make,
                    'model' => $model,
                    'colour' => $colour,
                    'fuel' => $fuel,
                ];

                // Collect fuel types for filtering
                $fuelTypes[$fuel] = true;

                // Check for valid registration format
                // The valid registration format is:
                // 2 letters followed by 2 numbers, followed by a space and then 3 letters
                if (preg_match('/^[A-Z]{2}\d{2} [A-Z]{3}$/', $registration)) {
                    $validRegistrations[] =  $carData[$registration];
                } else {
                    $invalidRegistrations[] = $registration;
                }
            }

        }
        fclose($handle);
    }

    // Export CSV files for each fuel type
    foreach ($fuelTypes as $fuel => $_) {
        exportCarsByFuelType($carData, $fuel);
    }

    return [
        'vehicles_valid_registrations' => $validRegistrations,
        'vehicles_invalid_registration_count' => count($invalidRegistrations),
    ];
}

/**
 * Exports cars with a specific fuel type to a CSV file.
 *
 * @param array $carData The car data.
 * @param string $fuelType The fuel type to filter by.
 */
function exportCarsByFuelType(array $carData, string $fuelType): void
{
    $filteredCars = [];
    foreach ($carData as $registration => $car) {
        if ($car['fuel'] === $fuelType) {
            $filteredCars[$registration] = $car;
        }
    }

    $filename = EXPORT_FOLDER_PATH . 'vehicles_' . strtolower($fuelType) . '.csv';
    $fp = fopen($filename, 'w');

    // Write header row
    fputcsv($fp, ['Car Registration', 'Make', 'Model', 'Colour', 'Fuel']);

    // Write data rows
    foreach ($filteredCars as $registration => $car) {
        $row = array_merge([$registration], $car);
        fputcsv($fp, $row);
    }

    fclose($fp);
}

$results = importAndAnalyzeCarData(CSV_FILE_PATH);
print_r($results);
