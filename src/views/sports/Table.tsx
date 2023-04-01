// ** MUI Imports
import Card from '@mui/material/Card';
import Chip from '@mui/material/Chip';
import Table from '@mui/material/Table';
import TableRow from '@mui/material/TableRow';
import TableHead from '@mui/material/TableHead';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';

// ** Types Imports
import { ThemeColor } from '../../@core/layouts/types';

interface RowType {
  id?: number
  name?: string
  hasOfferings?: boolean
  leagueSpecialsCount?: number
  eventSpecialsCount?: number
  eventCount?: number
}

interface HasOffering {
    [key: string]: {
        color: ThemeColor
    }
}

const hasOffering: HasOffering = {
    yes: { color: "success" },
    no: { color: "error" }
}

const SportsTable = ({sports}: RowType[] | any) => {
    //console.log("Sports: ", sports)
  return (
    <Card>
      <TableContainer>
        <Table sx={{ minWidth: 800 }} aria-label='table in dashboard'>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Name</TableCell>
              <TableCell>Has Offerings</TableCell>
              <TableCell>League Special Count</TableCell>
              <TableCell>Event Special Count</TableCell>
              <TableCell>Event Count</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
          { sports && sports.map((row: RowType) => (
              <TableRow hover key={row.id} sx={{ '&:last-of-type td, &:last-of-type th': { border: 0 } }}>
                <TableCell>{row.id}</TableCell>
                <TableCell>{row.name}</TableCell>
                <TableCell>
                <Chip
                    label={row.hasOfferings?'YES':'NO'}
                    color={hasOffering[row.hasOfferings?'yes':'no'].color}
                    sx={{
                      height: 24,
                      fontSize: '0.75rem',
                      textTransform: 'capitalize',
                      '& .MuiChip-label': { fontWeight: 500 }
                    }}
                  />
                </TableCell>
                <TableCell>{row.leagueSpecialsCount}</TableCell>
                <TableCell>{row.eventSpecialsCount}</TableCell>
                <TableCell>{row.eventCount}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Card>
  )
}

export default SportsTable
