import React from "react";
import { Table } from "antd";
import ProgressBar from "./ProgressBar";
import style from "../../scss/_campaigntable.scss";


const table_columns = [
    {
        title: 'Campaigns in progress',
        dataIndex: 'campaign',
        key: 'campaign',
        render: text => <a class='campaign-name-link'>{text}</a>,
    },
    {
        title: 'Total students',
        dataIndex: 'students',
        key: 'students',
        sorter: (a, b) => a.students - b.students,
    },
    {
        title: () => {
            return (
                <div>
                    Progress
                    <div className="progress-icons-container">
                        <div className="progress-icon">
                            <span className="icon-dot completed-icon-dot"> </span>
                            <span className="completed-icon-label">Completed</span>
                        </div>
                        <div className="progress-icon">
                            <span className="icon-dot in-progress-icon-icon-dot"> </span>
                            <span className="in-progress-icon-icon-label">In progress</span>
                        </div>
                        <div className="progress-icon">
                            <span className="icon-dot not-started-icon-icon-dot"> </span>
                            <span className="not-started-icon-icon-label">Not started</span>
                        </div>
                    </div>
                </div>
            );
        },
        key: 'progress',
        dataIndex: 'progress',
        width: '40%',
        render: ( cell, row ) => { return (<ProgressBar readings={ row.progress } />) },
    },
    {
        title: 'Completion rate',
        dataIndex: 'rate',
        key: 'rate',
        sorter: (a, b) => parseFloat(a.rate) - parseFloat(b.rate),
    }
];

export default class CampaignTable extends React.Component {
    constructor(props) {
        super(props);

        this.columns = props.columns;
        this.dataSource = props.dataSource;

    }

    componentDidMount() {

    }

    render() {
        return (
            <Table
                columns={ table_columns }
                dataSource={this.dataSource}
                pagination={{ defaultPageSize: 3, showSizeChanger: true, pageSizeOptions: ['3', '5', '10']}}
            />
        );
    }
}