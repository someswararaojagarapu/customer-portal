import React, {useState,useEffect} from 'react';
import ServerFilters from './server_filter';
import {createRoot} from "react-dom/client";
import ReactPaginate from "react-paginate";

const perPage = 12;

const ServerList = () => {
    const [filteredServers, setFilteredServers] = useState([]);
    const [currentPage, setCurrentPage] = useState(0);
    const [isRendered, setIsRendered] = useState(false);

    useEffect(() => {
        setFilteredServers(serverInfoJson);
        setIsRendered(true);
    },[]);

    const applyFilters = async (filters) => {
        setIsRendered(false);
        await fetch(`${hostName}/api/server/information/list`, {
            method: 'POST',
            body: JSON.stringify(filters),
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then((response) => response.json())
            .then((data) => {
                setFilteredServers(data);
                setIsRendered(true);
            });
    };
    const startIndex = currentPage * perPage;
    const endIndex = startIndex + perPage;
    const slicedServers = filteredServers.slice(startIndex, endIndex);

    const handlePageClick = (data) => {
        const selectedPage = data.selected;
        setCurrentPage(selectedPage);
    };

    return (
        <>
            <ServerFilters onFilterChange={applyFilters}/>
            <main className="col-md-9 py-3 " style={{padding:'1rem 1rem 1rem 0'}}>
                <div className="bg-white p-3 h-100" style={{borderRadius:'10px'}}>
                    <h2 style={{borderBottom:'1px solid lightgray',paddingBottom:'10px'}}>Server List with information</h2>
                    {isRendered ? (
                        <>
                            <div className="row p-3">
                                {slicedServers.map((server, index) => (
                                    <div className="col-md-3 p-3" key={index}>
                                        <div className="h-100 p-3" style={{boxShadow: "0 0 12px 1px hsla(0,0%,48%,.5",borderRadius:'7px'}} key={index}>
                                            <h4>{server.Model}</h4>
                                            <p>Ram: {server.RAM}</p>
                                            <p>HDD: {server.HDD}</p>
                                            <p>Price: {server.Price}</p>
                                            <p>Location: {server.Location}</p>
                                        </div>
                                    </div>

                                ))}
                            </div>
                            {slicedServers.length > 0 && (
                                <div className="col-md-12">
                                    <ReactPaginate
                                        previousLabel={'Previous'}
                                        nextLabel={'Next'}
                                        breakLabel={'...'}
                                        breakClassName={'break-me'}
                                        pageCount={Math.ceil(filteredServers.length / perPage)}
                                        pageRangeDisplayed={5}
                                        onPageChange={handlePageClick}
                                        containerClassName={'pagination'}
                                        subContainerClassName={'pages pagination'}
                                        activeClassName={'active'}
                                    />
                                </div>
                            )}
                        </>
                    ) : (
                        <>
                            <h6>Loading...</h6>
                        </>
                    )}
                </div>
            </main>
        </>
    );
};


const rootElement = document.getElementById("root");
const root = createRoot(rootElement);
root.render(<ServerList/>);
