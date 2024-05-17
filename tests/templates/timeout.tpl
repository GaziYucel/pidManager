<button onclick="handleFetch();">Fetch</button>

<script>
	let timeOutHolder = 0;

	function handleFetch() {
		console.log('handleFetch executed');
		clearTimeout(timeOutHolder);
		timeOutHolder = setTimeout(() => {
			fetch('https://api.datacite.org/dois?query=doi:10.11570/18.0003')
				// fetch('https://jsonplaceholder.typicode.com/todos/1')
				.then(response => response.json())
				.then(responseData => {
					console.log(responseData);
				}).catch((error) => { console.log(error); });
		}, 300);
	}
</script>
