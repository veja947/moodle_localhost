import React from "react";
import { Table } from "antd";
import ProgressBar from "./ProgressBar";


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
        title: 'Progress',
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

    render() {
        return (
            <Table
                columns={ table_columns }
                dataSource={this.dataSource}
                pagination={{ defaultPageSize: 3, showSizeChanger: true, pageSizeOptions: ['3', '10', '20']}}
            />
        );
    }
}