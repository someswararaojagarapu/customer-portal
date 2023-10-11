import React, {useState, useEffect} from 'react';
import Slider from "rc-slider";

const ServerFilters = ({onFilterChange}) => {
    const [storageFilter, setStorageFilter] = useState([0, 72000]);
    const [ramFilters, setRamFilters] = useState([]);
    const [hardDiskTypeFilter, setHardDiskTypeFilter] = useState('');
    const [locationFilter, setLocationFilter] = useState('');
    const [range, setRange] = useState([0, 72000]);
    const handleSliderChange = (e) => {
        setStorageFilter(e)
        setRange(e);
        console.log(e);
    };

    const generateMarks = () => {
        const marks = {};
        const values = [0, 250, 500, 1000, 2000, 3000, 4000, 8000, 12000, 24000, 48000, 72000];

        values.forEach(value => {
            marks[value] = `${value / 1000}TB`;
        });

        return marks;
    };

    // useEffect(() => {
    //     // Call your function here
    //     handleFilterChange();
    // }, [ramFilters,hardDiskTypeFilter,locationFilter,storageFilter]); // This will run the effect whenever hardDiskTypeFilter changes


    const [filterOptions, setFilterOptions] = useState({
        Storage: [],
        Ram: [],
        HardDiskTypes: [],
        Location: []
    });

    useEffect(() => {
        fetch('https://localhost:8000/api/server/filter/list')
            .then((response) => response.json())
            .then((data) => {
                setFilterOptions(data)
            })
            .catch((error) => console.error('Error fetching filter options:', error));
    }, []);

    // Handle filter changes and call the onFilterChange callback
    const handleFilterChange = () => {
        const filters = {
            storage: storageFilter[0] + ' to ' + storageFilter[1],
            ram: ramFilters,
            hardDiskType: hardDiskTypeFilter,
            location: locationFilter
        };
        onFilterChange(filters);
    };

    return (
        <div className="col-md-3 p-3">
            <div id="sidebar" className="collapse collapse-horizontal show border-end bg-white h-100" style={{borderRadius:'10px'}}>
                <div id="sidebar-nav" className="list-group text-sm-start min-vh-100 p-3">
                    <h2 style={{borderBottom:'1px solid lightgray',paddingBottom:'10px'}}>Filters</h2>
                    <ul className="nav nav-pills flex-column mb-auto p-4">
                        <li className="nav-item py-2">
                            <h5>Storage Capacity (GB)</h5>
                            <Slider
                                range
                                min={0}
                                max={72000}
                                step={250}
                                value={range}
                                onChange={handleSliderChange}
                                marks={generateMarks()}
                            />
                            <br />
                            <p>Range: {range[0] / 1000}TB - {range[1] / 1000}TB</p>
                        </li>
                        <li className="nav-item py-2">

                            <div className="row">
                                <h5 className="form-label col-md-12">RAM</h5>
                                {filterOptions.Ram.map((ramOption) => (
                                    <label className="col-md-6 p-2"  key={ramOption}>
                                        <input
                                            className="form-check-input"
                                            type="checkbox"
                                            value={ramOption}
                                            checked={ramFilters.includes(ramOption)}
                                            onChange={(e) => {
                                                const selectedRam = e.target.value;
                                                setRamFilters((prevFilters) =>
                                                    prevFilters.includes(selectedRam)
                                                        ? prevFilters.filter((filter) => filter !== selectedRam)
                                                        : [...prevFilters, selectedRam]
                                                );
                                            }}
                                            style={{margin:'2px 5px'}}
                                        />
                                        {ramOption}
                                    </label>
                                ))}
                            </div>

                        </li>
                        <li className="nav-item py-2">
                            <h5 className="form-label">Hard Disk Type</h5>
                            <select className="form-select"
                                    value={hardDiskTypeFilter}
                                    onChange={(e) => setHardDiskTypeFilter(e.target.value)}
                            >
                                <option value="">All</option>
                                {filterOptions.HardDiskTypes.map((type) => (
                                    <option key={type} value={type}>
                                        {type}
                                    </option>
                                ))}
                            </select>
                        </li>
                        <li className="nav-item py-2">
                            <h5 className="form-label">Location</h5>
                            <select className="form-select"
                                    value={locationFilter}
                                    onChange={(e) => {
                                        setLocationFilter(e.target.value)
                                    }}
                            >
                                <option value="">All</option>
                                {filterOptions.Location.map((location) => (
                                    <option key={location} value={location}>
                                        {location}
                                    </option>
                                ))}
                            </select>
                        </li>
                        <li className="nav-item py-2">
                            <button className="btn btn-primary" onClick={handleFilterChange}>Apply Filters</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    );
};

export default ServerFilters;