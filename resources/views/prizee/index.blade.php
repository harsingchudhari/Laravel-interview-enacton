<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .chart-container {
            width: 100%;
            max-width: 500px;
            height: 400px;
            margin: 0 auto;
        }
        .sortable:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        .ascending::after {
            content: " \25B2";
        }
        .descending::after {
            content: " \25BC";
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Prize Distribution Management</h4>

                        <a href="{{ route('prizee.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add New Prize
                        </a>
                    </div>

                    @if (session('success'))
                    <div id="success-alert" 
                                class="alert alert-success m-2" 
                                style="padding:10px; margin-bottom:10px; border-radius:5px;">
                                {{ session('success') }}
                            </div>
                     @endif

                    <div class="alert alert-info m-2">
                        <strong>Total probability Used:</strong>{{ isset($existingTotal) ? $existingTotal : '' }}% 
                        <strong>Remaining probability:</strong> {{ isset($remaining) ? $remaining : '' }}%
                    </div>

                    <table class="table" id="prizesTable">
                        <thead class="">    
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Probability</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($prizeedata))
                            @foreach($prizeedata as $index => $prizee)
                                <tr data-prizee-id="{{ $prizee->id }}">
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $prizee->title }}</td>
                                    <td>{{ $prizee->probability }}%</td>
                                    <td>
                                        <a href="{{ route('prizee.edit', $prizee->id) }}"
                                           class="btn btn-warning btn-sm text-white">
                                           <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('prizee.delete', $prizee->id) }}"
                                              method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to delete this prize?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            <div class="sumliutor mt-3 d-flex justify-content-center border alert">
                <div class="text-center">
                   <h4>Simulation Calculation</h4>
                    <div class="mb-3 border alert">
                        <label for="prize" class="form-label">Number of Prizes to Distribute</label>
                        <input type="number" class="form-control" id="prize" name="prize"
                            placeholder="Enter number of prizes" required style="width: 250px; margin: 0 auto;">
                    </div>

                 <button class="btn btn-success mt-2" id="submitPrize">
                     <i class="fas fa-play-circle"></i> Simulate Distribution
                 </button>
                 <button class="btn btn-secondary mt-2" id="resetSimulation" type="button">
                     <i class="fas fa-redo"></i> Reset
                 </button>
                    
                    <div id="simulationStats" class="mt-3 p-3 bg-light border rounded d-none">
                        <h5>Simulation Statistics</h5>
                        <p id="statsContent" class="mb-0"></p>
                    </div>

                </div>
            </div>

        <div class="piecharit mt-5 text-center d-flex">

            <div class="chart-container">
                <h3>Prize Distribution Chart (Percentages)</h3>
                <canvas id="myChart"></canvas>
            </div>

            <div class="chart-container">
                <h3>Simulated Prize Distribution</h3>
                <canvas id="myChart2"></canvas>
                <div class="mt-3">
                    <p><small class="text-muted">This chart shows the actual distribution when distributing a specific number of prizes based on the configured probabilities.</small></p>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
   
   setTimeout(function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 5000);

     document.addEventListener("DOMContentLoaded", function () {
         
    
         const headers = document.querySelectorAll('#prizesTable thead th');
         headers.forEach((header, index) => {
             if (index < 3) {
                 header.classList.add('sortable');
                 header.addEventListener('click', function() {
                     sortTable(index);
                 });
             }
         });
         
         function sortTable(columnIndex) {
             const table = document.getElementById('prizesTable');
             const tbody = table.querySelector('tbody');
             const rows = Array.from(tbody.querySelectorAll('tr'));
          
             headers.forEach(header => {
                 header.classList.remove('ascending', 'descending');
             });
             
             const isAscending = table.getAttribute('data-sort-order') !== 'asc';
             table.setAttribute('data-sort-order', isAscending ? 'asc' : 'desc');
             
             headers[columnIndex].classList.add(isAscending ? 'ascending' : 'descending');
             
             rows.sort((a, b) => {
                 const aText = a.cells[columnIndex].textContent.trim();
                 const bText = b.cells[columnIndex].textContent.trim();
                 
          
                 if (columnIndex === 2) {
                     const aNum = parseFloat(aText);
                     const bNum = parseFloat(bText);
                     return isAscending ? aNum - bNum : bNum - aNum;
                 }
                 
                 return isAscending ? 
                     aText.localeCompare(bText) : 
                     bText.localeCompare(aText);
             });
            
             rows.forEach(row => tbody.appendChild(row));
         }

    const labels = @json(isset($prizeedata) ? $prizeedata->pluck('title') : []);
    const percentages = @json(isset($prizeedata) ? $prizeedata->pluck('probability') : []);

    const colors = percentages.map(() =>
        '#' + Math.floor(Math.random() * 16777215).toString(16)
    );

  
    function legendWithPercentage(chart) {
        const data = chart.data;
        const dataset = data.datasets[0];

        return data.labels.map((label, i) => {
            const value = dataset.data[i];
            return {
                text: `${label} (${value}%)`,
                fillStyle: dataset.backgroundColor[i],
                strokeStyle: dataset.backgroundColor[i],
                lineWidth: 1
            };
        });
    }

 
    new Chart(document.getElementById('myChart'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: percentages,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        generateLabels: legendWithPercentage
                    }
                }
            }
        }
    });

    const prizeInput = document.getElementById("prize");

    document.getElementById("submitPrize").addEventListener("click", function (event) {
        event.preventDefault();
        generateAccurateChart();
    });
   
    document.getElementById("resetSimulation").addEventListener("click", function () {
        document.getElementById("prize").value = "";
        if (window.accurateChart) {
            window.accurateChart.destroy();
            window.accurateChart = null;
        }
        
        document.getElementById("simulationStats").classList.add("d-none");
    });

    function generateAccurateChart() {
        const totalPrize = parseInt(prizeInput.value);

        if (!totalPrize || totalPrize <= 0) {
            alert("Please enter a valid number of prizes (greater than 0)");
            return;
        }
     
        let accurateCounts = new Array(percentages.length).fill(0);
        let remainingPrizes = totalPrize;
        let remainingPercentages = [...percentages];
        
     
        for (let i = 0; i < percentages.length; i++) {
            const exactValue = (percentages[i] / 100) * totalPrize;
            accurateCounts[i] = Math.floor(exactValue);
            remainingPrizes -= accurateCounts[i];
            remainingPercentages[i] = (exactValue - Math.floor(exactValue)) * 100;
        }
        
    
        const sortedIndices = remainingPercentages
            .map((fraction, index) => ({ fraction, index }))
            .sort((a, b) => b.fraction - a.fraction)
            .map(item => item.index);
        
        
        for (let i = 0; i < remainingPrizes && i < sortedIndices.length; i++) {
            accurateCounts[sortedIndices[i]]++;
        }
 
        const totalActual = accurateCounts.reduce((sum, count) => sum + count, 0);
        const maxDeviation = Math.max(...percentages.map((p, i) => {
            const expected = (p / 100) * totalPrize;
            return Math.abs(expected - accurateCounts[i]);
        }));
        
        const statsContent = `
            Total Prizes Distributed: ${totalActual}<br>
            Maximum Deviation from Expected: ${maxDeviation.toFixed(2)} prizes<br>
            Simulation Method: Largest Remainder (Most Accurate)
        `;
        
        document.getElementById("statsContent").innerHTML = statsContent;
        document.getElementById("simulationStats").classList.remove("d-none");

        const ctx2 = document.getElementById('myChart2').getContext('2d');

        if (window.accurateChart) window.accurateChart.destroy();

        window.accurateChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: accurateCounts,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels(chart) {
                                return chart.data.labels.map((label, i) => ({
                                    text: `${label} (${accurateCounts[i]} prizes)`,
                                    fillStyle: chart.data.datasets[0].backgroundColor[i]
                                }));
                            }
                        }
                    }
                }
            }
        });

    }
});

    </script>

</body>

</html>
