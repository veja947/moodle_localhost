import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";
import Select from 'react-select';
import { AddBox, ArrowDownward } from "@material-ui/icons";
import MaterialTable from 'material-table';

// const tableIcons = {
//     Add: forwardRef((props, ref) => <AddBox {...props} ref={ref} />),
//     Check: forwardRef((props, ref) => <Check {...props} ref={ref} />),
//     Clear: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
//     Delete: forwardRef((props, ref) => <DeleteOutline {...props} ref={ref} />),
//     DetailPanel: forwardRef((props, ref) => <ChevronRight {...props} ref={ref} />),
//     Edit: forwardRef((props, ref) => <Edit {...props} ref={ref} />),
//     Export: forwardRef((props, ref) => <SaveAlt {...props} ref={ref} />),
//     Filter: forwardRef((props, ref) => <FilterList {...props} ref={ref} />),
//     FirstPage: forwardRef((props, ref) => <FirstPage {...props} ref={ref} />),
//     LastPage: forwardRef((props, ref) => <LastPage {...props} ref={ref} />),
//     NextPage: forwardRef((props, ref) => <ChevronRight {...props} ref={ref} />),
//     PreviousPage: forwardRef((props, ref) => <ChevronLeft {...props} ref={ref} />),
//     ResetSearch: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
//     Search: forwardRef((props, ref) => <Search {...props} ref={ref} />),
//     SortArrow: forwardRef((props, ref) => <ArrowDownward {...props} ref={ref} />),
//     ThirdStateCheck: forwardRef((props, ref) => <Remove {...props} ref={ref} />),
//     ViewColumn: forwardRef((props, ref) => <ViewColumn {...props} ref={ref} />)
// };

const column =[
    { title: 'Campaigns in progress', field: 'campaigns' },
    { title: 'Total students', field: 'total_students', type: 'numeric' },
    { title: 'Progress', field: 'progress' },
    { title: 'Completion rate', field: 'completion_rate' }
];

const data = [
    { campaigns: "program1", total_students: 123, progress: '', completion_rate: "54%" },
    { campaigns: "program2", total_students: 456, progress: '', completion_rate: "12%" },
    { campaigns: "program3", total_students: 321, progress: '', completion_rate: "44%" },
    { campaigns: "program4", total_students: 436, progress: '', completion_rate: "78%" },
    { campaigns: "program5", total_students: 856, progress: '', completion_rate: "99%" },
];

const options = [
    { value: 'program111', label: 'program111' },
    { value: 'program222', label: 'program222' },
    { value: 'program333', label: 'program333' }
];

class App extends Component {


    render() {
        return (
            <Router>
                <header>
                    <div>
                        <p>Student Activity</p>
                    </div>
                    <div>
                        <p>Updated on xxxx-xx-xx</p>
                        <div>
                            <Select options={options} />
                        </div>
                    </div>
                </header>
                <main>
                    <MaterialTable
                        columns={column}
                        data={data}
                        title="Demo Title"
                    />
                    <Switch>
                        <Route path="/">
                            <h5>new Dashboard</h5>
                        </Route>
                    </Switch>
                </main>
            </Router>
        );
    }
}

export default App
