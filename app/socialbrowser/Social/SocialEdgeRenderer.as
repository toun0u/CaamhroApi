package
{
	import mx.core.UIComponent;
	import com.adobe.flex.extras.controls.springgraph.IEdgeRenderer;
	import com.adobe.flex.extras.controls.springgraph.Graph;
	import flash.display.Graphics;
	import com.adobe.flex.extras.controls.springgraph.Item;
	import mx.core.IDataRenderer;

	public class SocialEdgeRenderer implements IEdgeRenderer
	{
		public  function draw(g:Graphics, fromView:UIComponent, toView:UIComponent, fromX:int, fromY:int, toX:int, toY:int, graph:Graph):Boolean
		{
			
			var data:Object = graph.getLinkData((fromView as IDataRenderer).data as Item, (toView as IDataRenderer).data as Item);
			var col:uint = 0xFFFFFF;
			var alfa:Number=1.0;
			var lt:int = 1;
			if (data){
				if (data.type){
					if (data.type == 1){
						col = 0xffff66;
					} else if (data.type == 2){
						col= 0x3300cc;
					}else if (data.type == 3){
						col= 0xff0000;
					}else if (data.type == 4){
						col= 0xcc66ff;
					}else if (data.type == 5){
						col= 0xff9933;
					} else if(data.type ==0) {
						col = 0x99ff66;
					}
				}
				if (data.size){
				if (data.size < 3){
					//alfa = 0.5
					lt =1
				} else if (data.size > 6 && data.ize < 10){
					
					lt =2
				} else {
					
					lt =3;
				}
				}
			}
			g.lineStyle(3,col, alfa);
			g.beginFill(0);
			g.moveTo(fromX, fromY);
		    g.lineTo(toX, toY);
		    g.endFill();
		    g.drawCircle(fromX+(toX -fromX)/2, fromY+(toY-fromY)/2, 2);
		    //g.clear();
			return true;
		}
		
	}
}