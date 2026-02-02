export default class Main {
    initialize() {
        console.log('radimn')
        this.addListenerToMollieTable()
    }

    addListenerToMollieTable() {
        var $reportsTable = $('table').DataTable();
        $reportsTable.on('draw', function () {
            if (document.querySelector('.dataTables_empty')) {
                $reportsTable.page('first').draw('false');
            }
        })
    }
}
