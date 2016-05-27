package
{
	import com.adobe.flex.extras.controls.springgraph.IEdgeClicker;
	import com.adobe.flex.extras.controls.springgraph.GraphEdge;
	import com.adobe.flex.extras.controls.springgraph.GraphNode;
	import com.adobe.flex.extras.controls.springgraph.Graph;
	import com.adobe.flex.extras.controls.springgraph.Item;
	import mx.core.Application;
	import mx.core.IDataRenderer;
	import flash.net.navigateToURL;
	import flash.net.URLRequest;
	import com.adobe.flex.extras.controls.springgraph.Roamer;
	import mx.utils.URLUtil;
	

	public class EdgeClicker implements IEdgeClicker
	{
		private var roamer:Roamer;
		public function EdgeClicker(rm: Roamer){
			roamer = rm;
		}
		public function edgeClick(edge:GraphEdge, graph:Graph):void
		{
			var f:GraphNode = GraphNode(edge.getFrom());
				var t:GraphNode = GraphNode(edge.getTo());
				var fromItem: Item = (f.view as IDataRenderer).data as Item;
			var toItem: Item = (t.view as IDataRenderer).data as Item;
			
			var data: Object = graph.getLinkData(fromItem, toItem);
			if (data != null) {
				var page:String="http://langtech.jrc.it/entities/socNet/"+data.url;
				navigateToURL(new URLRequest(page), '_blank');
			}
		}
		
	}
}