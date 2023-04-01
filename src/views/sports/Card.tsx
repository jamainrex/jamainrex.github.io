// ** MUI Imports
import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import Typography from '@mui/material/Typography';
import CardContent from '@mui/material/CardContent';
import JSONPretty from 'react-json-pretty';
import 'react-json-pretty/themes/monikai.css';

const CardLeague = ( {data} : any ) => {
  return (
    <Card sx={{ position: 'relative' }}>
      <CardContent>
        <Box
          sx={{
            mt: 2.75,
            mb: 2.75,
            display: 'flex',
            flexWrap: 'wrap',
            alignItems: 'center',
            justifyContent: 'space-between'
          }}
        >
          <Box sx={{ mr: 2, mb: 1, display: 'flex', flexDirection: 'column' }}>
            <Typography variant='h6'>{data.name}</Typography>
            <Typography variant='caption'>ID: {data.id}</Typography>
            <Typography variant='caption'>Events:</Typography>
          </Box>
        </Box>
        <Box sx={{ gap: 2, display: 'flex', flexWrap: 'wrap', justifyContent: 'space-between', alignItems: 'left', maxHeight: 500, overflow: 'auto', backgroundColor: '#272822' }}>
          <JSONPretty id="json-pretty" data={data.events} ></JSONPretty>
        </Box>
      </CardContent>
    </Card>
  )
}

export default CardLeague
