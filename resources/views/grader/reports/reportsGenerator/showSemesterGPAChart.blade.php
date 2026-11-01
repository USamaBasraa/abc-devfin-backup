<div >


    

    
</div>


<script>


function makeChart111111111111111111111111(data,selectedValue) {
    let totalStudentsData = {
    '1.5': undefined,
    '2.0': undefined,
    '2.5': undefined,
    '3.0': undefined,
    '3.5': undefined,
    '4.0': undefined
};
for (let index = 0; index < data.length; index++) {
    const cgpa = data[index].cgpa;

    if (cgpa >= 0 && cgpa <= 1.5) {
        totalStudentsData['1.5'] === undefined ? totalStudentsData['1.5'] = 1 : totalStudentsData['1.5'] += 1;
    } else if (cgpa > 1.5 && cgpa <= 2.0) {
        totalStudentsData['2.0'] === undefined ? totalStudentsData['2.0'] = 1 : totalStudentsData['2.0'] += 1;
    } else if (cgpa > 2.0 && cgpa <= 2.5) {
        totalStudentsData['2.5'] === undefined ? totalStudentsData['2.5'] = 1 : totalStudentsData['2.5'] += 1;
    } else if (cgpa > 2.5 && cgpa <= 3.0) {
        totalStudentsData['3.0'] === undefined ? totalStudentsData['3.0'] = 1 : totalStudentsData['3.0'] += 1;
    } else if (cgpa > 3.0 && cgpa <= 3.5) {
        totalStudentsData['3.5'] === undefined ? totalStudentsData['3.5'] = 1 : totalStudentsData['3.5'] += 1;
    } else if (cgpa > 3.5 && cgpa <= 4.0) {
        totalStudentsData['4.0'] === undefined ? totalStudentsData['4.0'] = 1 : totalStudentsData['4.0'] += 1;
    }
}
    // Convert totalStudentsData into CanvasJS dataPoints format
    const dataPoints = Object.entries(totalStudentsData).map(([x, y]) => ({ label: x, y }));
    // Create a line chart
    const chart = new CanvasJS.Chart("chartContainer1", {
    animationEnabled: true,
    zoomEnabled: true,
    data: [{
        type: "line",
        color: "#007bff",
        dataPoints: dataPoints,
        markerType: "circle",
        markerSize: 8,
        lineColor: '#007bff',
        toolTipContent: "{label}: {y} students",
        // Additional code to show count on each data point
        markerLabel: {
            backgroundColor: "transparent",
            fontColor: "#000",
            fontWeight: "bold",
            content: "{y}",
            padding: 5
        }
    }],
    axisX: {
        title: "CGPA Ranges",
        labelAngle: 0
    },
    axisY: {
        title: "Number of Students",
        minimum: 0
    }
});
}

</script>
